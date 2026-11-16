<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Contracts\XeroGateway;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Data\Contracts\HasXeroEndpoint;
use Dcodegroup\XeroIntegration\Data\Traits\SyncsWithXeroApi;
use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Enums\XeroTimesheetStatusEnum;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Exceptions\XeroValidationException;
use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use LogicException;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Optional;
use Throwable;
use XeroPHP\Remote\Model as XeroModel;

/**
 * A Payroll AU 2.0 timesheet: one employee, one pay period, one line per date and earnings rate.
 *
 * The 2.0 API has no update call for the timesheet itself. Re-sending a period reconciles the existing
 * timesheet's lines (create, update, delete), reverting an Approved timesheet to Draft first. It is only
 * approved again when `status` is explicitly set to Approved.
 */
class XeroTimesheetData extends AbstractXeroData implements HasXeroEndpoint
{
    use SyncsWithXeroApi;

    protected string $key = 'timesheetID';

    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::TIMESHEET_AU;
    }

    public static function getXeroEndpoint(): XeroEndpoint
    {
        return new XeroEndpoint(
            api: XeroApiEnum::PAYROLL_AU_V2,
            resource: 'Timesheets',
            idKey: 'timesheetID',
            guidFilters: ['employeeId', 'payrollCalendarId'],
            paginated: true,
        );
    }

    public function __construct(
        public string $employeeID,
        public string $payrollCalendarID,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $startDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $endDate,
        /** @var Collection<int, XeroTimesheetLineData> */
        public Collection $timesheetLines,
        public string|Optional|null $timesheetID = null,
        #[WithCast(EnumCast::class)]
        public XeroTimesheetStatusEnum|Optional|null $status = null,
        public float|Optional|null $totalHours = null,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon|Optional|null $updatedDateUTC = null,
    ) {}

    /**
     * Build a timesheet for one pay period on the given (already resolved) earnings rate.
     *
     * There is one line per day with hours, in date order. Hours are rounded to 2 decimals and days without
     * hours get no line.
     *
     * @param  array<string, int|float>  $hoursByDate  Hours keyed by Y-m-d date.
     *
     * @throws XeroValidationException When there are no hours, or a date falls outside the period.
     */
    public static function forPeriod(
        string $employeeId,
        string $payrollCalendarId,
        Carbon $start,
        Carbon $end,
        string $earningsRateId,
        array $hoursByDate,
    ): self {
        $periodStart = Carbon::parse($start->toDateString())->startOfDay();
        $periodEnd = Carbon::parse($end->toDateString())->startOfDay();

        if ($periodEnd->lt($periodStart)) {
            throw new XeroValidationException('The pay period ends before it starts.');
        }

        if (empty($hoursByDate)) {
            throw new XeroValidationException('A timesheet needs at least one day of hours.');
        }

        $hoursByDay = [];

        foreach ($hoursByDate as $date => $hours) {
            $day = Carbon::parse((string) $date)->startOfDay();

            if ($day->lt($periodStart) || $day->gt($periodEnd)) {
                throw new XeroValidationException("Date {$day->toDateString()} is outside the pay period {$periodStart->toDateString()} to {$periodEnd->toDateString()}.");
            }

            $key = $day->toDateString();
            $hoursByDay[$key] = ($hoursByDay[$key] ?? 0) + (float) $hours;
        }

        ksort($hoursByDay);

        $lines = collect($hoursByDay)
            ->map(fn (float $hours): float => round($hours, 2))
            ->filter(fn (float $hours): bool => $hours > 0)
            ->map(fn (float $hours, string $date): XeroTimesheetLineData => new XeroTimesheetLineData(
                date: Carbon::parse($date)->startOfDay(),
                earningsRateID: $earningsRateId,
                numberOfUnits: $hours,
            ))
            ->values();

        if ($lines->isEmpty()) {
            throw new XeroValidationException('A timesheet needs at least one day with hours above zero.');
        }

        return new self(
            employeeID: $employeeId,
            payrollCalendarID: $payrollCalendarId,
            startDate: $periodStart,
            endDate: $periodEnd,
            timesheetLines: $lines,
        );
    }

    /**
     * Send the timesheet to Xero, reconciling with the existing timesheet for the same employee and period.
     *
     * An update never approves on its own: an Approved timesheet whose lines change is left in Draft unless `status`
     * is explicitly set to Approved. An update that changes nothing leaves the timesheet as it is.
     * Concurrent sends for the same employee and period are serialised with a cache lock, which needs a cache store
     * shared by every process that sends timesheets (such as Redis or the database).
     *
     * @throws XeroValidationException When the existing timesheet is Completed, or the given ID belongs to another employee or period.
     * @throws XeroIntegrationException When updating an Approved timesheet fails after it was reverted to Draft.
     * @throws LockTimeoutException When another send for the same employee and period holds the lock for over 30 seconds.
     */
    public function sendToXero(bool $syncRelated = true): void
    {
        Cache::lock($this->lockKey(), 300)->block(30, function (): void {
            $existing = $this->findExistingXeroTimesheet();

            if ($existing === null) {
                $this->createInXero();

                return;
            }

            if ($existing->status instanceof XeroTimesheetStatusEnum && $existing->status->isLocked()) {
                throw new XeroValidationException(
                    "The Xero timesheet for {$this->startDate->toDateString()} to {$this->endDate->toDateString()} is already {$existing->status->getLabel()} and can no longer be changed."
                );
            }

            $this->updateInXero($existing);
        });
    }

    protected function lockKey(): string
    {
        return strtolower("xero-integration:timesheet:{$this->employeeID}:{$this->startDate->toDateString()}:{$this->endDate->toDateString()}");
    }

    protected function createInXero(): void
    {
        $approve = $this->status === XeroTimesheetStatusEnum::APPROVED;

        $saved = $this->gateway()->save(static::getXeroEndpoint(), $this->toXeroPayload());

        $this->adopt(static::fromXeroArray($saved));

        if ($approve) {
            $this->approveInXero();
        }
    }

    /**
     * Update the existing timesheet's lines, reverting it to Draft first only when lines have to change.
     *
     * Nothing is sent when the lines already match, so an unchanged re-send leaves an Approved timesheet Approved.
     * When lines change, an Approved timesheet is reverted to Draft and stays there unless `status` is explicitly
     * Approved. An explicit Draft status also reverts an Approved timesheet that needs no line changes.
     */
    protected function updateInXero(self $existing): void
    {
        $id = (string) $existing->timesheetID;
        $wasApproved = $existing->status === XeroTimesheetStatusEnum::APPROVED;
        $approve = $this->status === XeroTimesheetStatusEnum::APPROVED;
        $plan = $this->planLineChanges($id, $existing->timesheetLines);

        $this->timesheetID = $id;

        if ($plan === []) {
            if ($wasApproved && $this->status === XeroTimesheetStatusEnum::DRAFT) {
                $this->gateway()->action(static::getXeroEndpoint(), 'POST', [$id, 'RevertToDraft']);
            } elseif (! $wasApproved && $approve) {
                $this->approveInXero();

                return;
            }

            $this->adopt(static::find($id) ?? $existing);

            return;
        }

        try {
            if ($wasApproved) {
                $this->gateway()->action(static::getXeroEndpoint(), 'POST', [$id, 'RevertToDraft']);
            }

            foreach ($plan as [$method, $segments, $body]) {
                $this->gateway()->action(static::getXeroEndpoint(), $method, $segments, $body);
            }

            if ($approve) {
                $this->approveInXero();

                return;
            }

            $this->adopt(static::find($id) ?? $existing);
        } catch (Throwable $failure) {
            throw $wasApproved ? $this->leftInDraft($id, $failure) : $failure;
        }
    }

    /**
     * A timesheet that was reverted and then could not be updated stays in Draft: approving half-updated hours would
     * put wrong hours into a pay run. The failure is kept as the previous exception and its message is not repeated,
     * as it can carry request details.
     */
    protected function leftInDraft(string $id, Throwable $failure): XeroIntegrationException
    {
        return new XeroIntegrationException(
            "The Xero timesheet {$id} may have been reverted to Draft and could not be fully updated. Check it in Xero, then send it again with the status Approved to finish.",
            0,
            $failure,
        );
    }

    protected function approveInXero(): void
    {
        $endpoint = static::getXeroEndpoint();

        $response = $this->gateway()->action($endpoint, 'POST', [(string) $this->timesheetID, 'Approve']);

        $saved = $response[$endpoint->api->itemKey($endpoint->resource)] ?? null;

        $this->adopt($saved === null ? (static::find((string) $this->timesheetID) ?? $this) : static::fromXeroArray($saved));
    }

    /**
     * The calls that make the timesheet's lines in Xero match this timesheet: one line per date and earnings rate.
     *
     * Lines are created and updated before any are deleted, so a failure part way leaves extra hours rather than
     * missing ones.
     *
     * @param  Collection<int, XeroTimesheetLineData>  $existingLines
     * @return list<array{0: string, 1: array<int, string>, 2: array<string, mixed>}> Method, path segments and body of each call, in order.
     */
    protected function planLineChanges(string $id, Collection $existingLines): array
    {
        $desired = $this->timesheetLines->keyBy(fn (XeroTimesheetLineData $line): string => $line->lineKey());
        $existingByKey = $existingLines->groupBy(fn (XeroTimesheetLineData $line): string => $line->lineKey());
        $changes = [];
        $deletions = [];

        foreach ($existingByKey as $key => $group) {
            $wanted = $desired->get($key);

            foreach ($group as $index => $line) {
                $lineSegments = [$id, 'Lines', (string) $line->timesheetLineID];

                if ($wanted === null || $index > 0) {
                    $deletions[] = ['DELETE', $lineSegments, []];
                } elseif (abs($line->numberOfUnits - $wanted->numberOfUnits) > 0.00001) {
                    $changes[] = ['PUT', $lineSegments, $wanted->toXeroPayload()];
                }
            }
        }

        foreach ($desired as $key => $line) {
            if (! $existingByKey->has($key)) {
                $changes[] = ['POST', [$id, 'Lines'], $line->toXeroPayload()];
            }
        }

        return [...$changes, ...$deletions];
    }

    /**
     * The Xero timesheet this one would update, with its lines, or null when there is none.
     */
    protected function findExistingXeroTimesheet(): ?self
    {
        if (! empty($this->timesheetID) && is_string($this->timesheetID)) {
            if (! preg_match('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}$/iD', $this->timesheetID)) {
                throw new XeroValidationException('The given timesheet ID is not a valid GUID.');
            }

            $existing = static::find($this->timesheetID);

            if ($existing !== null && ! $this->isSameEmployeeAndPeriod($existing)) {
                throw new XeroValidationException('The given timesheet ID belongs to a different employee or pay period.');
            }

            return $existing;
        }

        $candidate = static::list([
            'employeeId' => $this->employeeID,
            'payrollCalendarId' => $this->payrollCalendarID,
            'startDate' => $this->startDate->toDateString(),
            'endDate' => $this->endDate->toDateString(),
        ])->first(
            fn (self $timesheet): bool => $this->isSameEmployeeAndPeriod($timesheet)
        );

        return $candidate === null ? null : static::find((string) $candidate->timesheetID);
    }

    protected function isSameEmployeeAndPeriod(self $other): bool
    {
        return strcasecmp($other->employeeID, $this->employeeID) === 0
            && strcasecmp($other->payrollCalendarID, $this->payrollCalendarID) === 0
            && $other->startDate->toDateString() === $this->startDate->toDateString()
            && $other->endDate->toDateString() === $this->endDate->toDateString();
    }

    /**
     * Take the Xero-assigned values from a saved timesheet and link the local model to it.
     */
    protected function adopt(self $saved): void
    {
        $this->timesheetID = $saved->timesheetID;
        $this->status = $saved->status;
        $this->totalHours = $saved->totalHours;
        $this->updatedDateUTC = $saved->updatedDateUTC;

        if ($saved->timesheetLines->isNotEmpty()) {
            $this->timesheetLines = $saved->timesheetLines;
        }

        if (is_string($this->timesheetID) && $this->timesheetID !== '') {
            $this->linkLocalModelToXero($this->timesheetID);
        }
    }

    protected function gateway(): XeroGateway
    {
        return app(XeroGateway::class);
    }

    /**
     * @throws LogicException Timesheets are only available through the Payroll AU 2.0 API, which xero-php does not support.
     */
    public static function fromXero(XeroModel $xeroObject): self
    {
        throw new LogicException('Timesheets are only available through the Payroll AU 2.0 API. Use fromXeroArray().');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromXeroArray(array $payload): self
    {
        return new self(
            employeeID: $payload['employeeID'],
            payrollCalendarID: $payload['payrollCalendarID'],
            startDate: Carbon::parse($payload['startDate'])->startOfDay(),
            endDate: Carbon::parse($payload['endDate'])->startOfDay(),
            timesheetLines: collect($payload['timesheetLines'] ?? [])
                ->map(fn (array $line): XeroTimesheetLineData => XeroTimesheetLineData::fromXeroArray($line))
                ->values(),
            timesheetID: $payload['timesheetID'] ?? null,
            status: static::resolveStatus($payload['status'] ?? null),
            totalHours: isset($payload['totalHours']) ? (float) $payload['totalHours'] : null,
            updatedDateUTC: isset($payload['updatedDateUTC']) ? Carbon::parse($payload['updatedDateUTC'], 'UTC') : null,
        );
    }

    /**
     * The create body. Status, total hours and the updated date are read-only in Xero, so they are not sent.
     *
     * @return array<string, mixed>
     */
    public function toXeroPayload(): array
    {
        return [
            'payrollCalendarID' => $this->payrollCalendarID,
            'employeeID' => $this->employeeID,
            'startDate' => $this->startDate->toDateString(),
            'endDate' => $this->endDate->toDateString(),
            'timesheetLines' => $this->timesheetLines
                ->map(fn (XeroTimesheetLineData $line): array => $line->toXeroPayload())
                ->values()
                ->all(),
        ];
    }

    public function toXeroArray(): array
    {
        return $this->toXeroPayload();
    }

    protected static function resolveStatus(mixed $status): ?XeroTimesheetStatusEnum
    {
        if ($status instanceof XeroTimesheetStatusEnum || $status === null) {
            return $status;
        }

        return XeroTimesheetStatusEnum::tryFrom(strtolower((string) $status));
    }
}
