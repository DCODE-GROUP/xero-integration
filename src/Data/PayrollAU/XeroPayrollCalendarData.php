<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeInterface;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Data\Contracts\HasXeroEndpoint;
use Dcodegroup\XeroIntegration\Data\Contracts\ListsXeroRecords;
use Dcodegroup\XeroIntegration\Data\Traits\CachesXeroLookups;
use Dcodegroup\XeroIntegration\Data\Traits\ReadsFromXeroApi;
use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Exceptions\XeroValidationException;
use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;
use LogicException;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use XeroPHP\Models\PayrollAU\PayrollCalendar as XeroPayrollCalendar;
use XeroPHP\Remote\Model as XeroModel;

/**
 * Read-only view of a Xero AU payroll calendar.
 */
class XeroPayrollCalendarData extends AbstractXeroData implements HasXeroEndpoint, ListsXeroRecords
{
    use CachesXeroLookups;
    use ReadsFromXeroApi;

    public function __construct(
        public string $PayrollCalendarID,
        public string $Name,
        public string $CalendarType,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $StartDate = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $PaymentDate = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $ReferenceDate = null,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public ?Carbon $UpdatedDateUTC = null,
    ) {}

    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::PAYROLL_CALENDAR_AU;
    }

    /**
     * @throws LogicException Payroll calendars are read through the direct API, not xero-php. Use fromXeroArray().
     */
    public static function fromXero(XeroModel $xeroObject): self
    {
        throw new LogicException('Payroll calendars are read through the direct API. Use fromXeroArray().');
    }

    /**
     * @throws LogicException Payroll calendars are read-only.
     */
    public function toXeroArray(): array
    {
        throw new LogicException('Payroll calendars are read-only and cannot be sent to Xero.');
    }

    public static function getXeroEndpoint(): XeroEndpoint
    {
        return new XeroEndpoint(
            api: XeroApiEnum::PAYROLL_AU,
            resource: 'PayrollCalendars',
            idKey: 'PayrollCalendarID',
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromXeroArray(array $payload): self
    {
        return new self(
            PayrollCalendarID: $payload['PayrollCalendarID'],
            Name: $payload['Name'],
            CalendarType: $payload['CalendarType'],
            StartDate: static::toCarbon($payload['StartDate'] ?? null),
            PaymentDate: static::toCarbon($payload['PaymentDate'] ?? null),
            ReferenceDate: static::toCarbon($payload['ReferenceDate'] ?? null),
            UpdatedDateUTC: static::toCarbon($payload['UpdatedDateUTC'] ?? null),
        );
    }

    /**
     * Find a calendar by ID in the per-tenant cached list.
     */
    public static function findCached(string $payrollCalendarId): ?self
    {
        return static::cached()->firstWhere('PayrollCalendarID', $payrollCalendarId);
    }

    /**
     * Work out the pay period containing the given date.
     *
     * Periods are stepped forwards or backwards from the calendar's anchor date (`ReferenceDate`,
     * falling back to `StartDate`), so the result is the same whichever period the anchor sits in.
     *
     * @return array{start: Carbon, end: Carbon}
     *
     * @throws XeroValidationException When the calendar has no anchor date or its type cannot be resolved safely.
     */
    public function payPeriodFor(Carbon $date): array
    {
        $anchor = $this->ReferenceDate ?? $this->StartDate;

        if ($anchor === null) {
            throw new XeroValidationException("Payroll calendar '{$this->Name}' has no start or reference date to calculate pay periods from.");
        }

        $anchor = Carbon::parse($anchor->toDateString(), 'UTC')->startOfDay();
        $target = Carbon::parse($date->toDateString(), 'UTC')->startOfDay();

        return match ($this->CalendarType) {
            XeroPayrollCalendar::CALENDARTYPE_WEEKLY => $this->dayBasedPeriod($anchor, $target, 7),
            XeroPayrollCalendar::CALENDARTYPE_FORTNIGHTLY => $this->dayBasedPeriod($anchor, $target, 14),
            XeroPayrollCalendar::CALENDARTYPE_FOURWEEKLY => $this->dayBasedPeriod($anchor, $target, 28),
            XeroPayrollCalendar::CALENDARTYPE_MONTHLY => $this->monthBasedPeriod($anchor, $target, 1),
            XeroPayrollCalendar::CALENDARTYPE_QUARTERLY => $this->monthBasedPeriod($anchor, $target, 3),
            default => throw new XeroValidationException("Payroll calendar '{$this->Name}' has type '{$this->CalendarType}', which cannot be resolved to pay periods safely."),
        };
    }

    /**
     * @return array{start: Carbon, end: Carbon}
     */
    protected function dayBasedPeriod(Carbon $anchor, Carbon $target, int $lengthInDays): array
    {
        $daysFromAnchor = intdiv($target->getTimestamp() - $anchor->getTimestamp(), 86400);
        $periodIndex = (int) floor($daysFromAnchor / $lengthInDays);

        $start = $anchor->copy()->addDays($periodIndex * $lengthInDays);

        return [
            'start' => $start,
            'end' => $start->copy()->addDays($lengthInDays - 1),
        ];
    }

    /**
     * @return array{start: Carbon, end: Carbon}
     *
     * @throws XeroValidationException
     */
    protected function monthBasedPeriod(Carbon $anchor, Carbon $target, int $lengthInMonths): array
    {
        if ($anchor->day > 28) {
            throw new XeroValidationException("Payroll calendar '{$this->Name}' is anchored on day {$anchor->day} of the month, which cannot be resolved to pay periods safely.");
        }

        $monthsFromAnchor = ($target->year - $anchor->year) * 12 + ($target->month - $anchor->month);
        $periodIndex = (int) floor($monthsFromAnchor / $lengthInMonths);

        $start = $anchor->copy()->addMonthsNoOverflow($periodIndex * $lengthInMonths);

        if ($start->gt($target)) {
            $start = $anchor->copy()->addMonthsNoOverflow(($periodIndex - 1) * $lengthInMonths);
        }

        return [
            'start' => $start,
            'end' => $start->copy()->addMonthsNoOverflow($lengthInMonths)->subDay(),
        ];
    }

    protected static function toCarbon(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::instance($value instanceof DateTimeInterface ? $value : new DateTimeImmutable($value));
    }
}
