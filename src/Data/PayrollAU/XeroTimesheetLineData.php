<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Contracts\XeroGateway;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Exceptions\XeroValidationException;
use Dcodegroup\XeroIntegration\Models\XeroRecord;
use Illuminate\Database\Eloquent\Model;
use LogicException;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Remote\Model as XeroModel;

/**
 * A Payroll AU 2.0 timesheet line: the units worked on one date on one earnings rate.
 *
 * A line can be written on its own, given only its timesheet's ID. Unlike `XeroTimesheetData::sendToXero()` this
 * never reverts or approves the timesheet, so Xero decides whether a locked timesheet accepts the change. Writes are
 * not locked: callers must serialise concurrent writes to the same timesheet themselves.
 */
class XeroTimesheetLineData extends AbstractXeroData
{
    protected string $key = 'timesheetLineID';

    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::TIMESHEET_LINE_AU;
    }

    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $date,
        public string $earningsRateID,
        public float $numberOfUnits,
        public string|Optional|null $timesheetLineID = null,
        public string|Optional|null $trackingItemID = null,
    ) {}

    /**
     * @throws LogicException Timesheet lines exist only in the 2.0 API, which xero-php does not support.
     */
    public static function fromXero(XeroModel $xeroObject): self
    {
        throw new LogicException('Timesheet lines are only available through the Payroll AU 2.0 API. Use fromXeroArray().');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromXeroArray(array $payload): self
    {
        return new self(
            date: Carbon::parse($payload['date'])->startOfDay(),
            earningsRateID: $payload['earningsRateID'],
            numberOfUnits: (float) $payload['numberOfUnits'],
            timesheetLineID: $payload['timesheetLineID'] ?? null,
            trackingItemID: $payload['trackingItemID'] ?? null,
        );
    }

    /**
     * The body Xero accepts when creating or updating the line. The line ID is part of the URL, not the body.
     *
     * @return array<string, mixed>
     */
    public function toXeroPayload(): array
    {
        return array_filter([
            'date' => $this->date->toDateString(),
            'earningsRateID' => $this->earningsRateID,
            'numberOfUnits' => $this->numberOfUnits,
            'trackingItemID' => $this->trackingItemID,
        ], fn (mixed $value): bool => $value !== null && ! $value instanceof Optional);
    }

    public function toXeroArray(): array
    {
        return $this->toXeroPayload();
    }

    /**
     * Link a local model to this line, so its Xero ID is recorded against the model when the line is saved.
     */
    public function forLocalModel(Model $model): static
    {
        $this->localModel = $model;

        return $this;
    }

    /**
     * Create the line on the timesheet, or update it when it has a `timesheetLineID`.
     *
     * Sets `timesheetLineID` from Xero's response and links the local model, if one was given.
     *
     * @throws XeroValidationException When the timesheet ID is not a GUID.
     * @throws XeroIntegrationException When Xero rejects the request or does not return the saved line.
     */
    public function sendToXero(string $timesheetId): static
    {
        static::assertTimesheetId($timesheetId);

        $lineId = $this->timesheetLineID;
        $isUpdate = is_string($lineId) && $lineId !== '';
        $endpoint = XeroTimesheetData::getXeroEndpoint();

        $response = $this->gateway()->action(
            $endpoint,
            $isUpdate ? 'PUT' : 'POST',
            $isUpdate ? [$timesheetId, 'Lines', $lineId] : [$timesheetId, 'Lines'],
            $this->toXeroPayload(),
        );

        $saved = $response['timesheetLine'] ?? $response;
        $savedId = $saved['timesheetLineID'] ?? null;

        if (! is_string($savedId) || $savedId === '') {
            throw new XeroIntegrationException('Xero did not return the saved timesheet line.');
        }

        $this->timesheetLineID = $savedId;

        $this->getLocalModel()?->xeroRecord()?->updateOrCreate( // @phpstan-ignore-line method.notFound
            ['xero_id' => $savedId],
            ['xero_id' => $savedId],
        );

        return $this;
    }

    /**
     * Delete the line from its timesheet and remove its local link.
     *
     * @throws XeroValidationException When the timesheet ID is not a GUID, or the line has no `timesheetLineID`.
     * @throws XeroIntegrationException When Xero rejects the request.
     */
    public function deleteFromXero(string $timesheetId): void
    {
        static::assertTimesheetId($timesheetId);

        $lineId = $this->timesheetLineID;

        if (! is_string($lineId) || $lineId === '') {
            throw new XeroValidationException('A timesheet line needs a timesheetLineID to be deleted.');
        }

        $this->gateway()->action(XeroTimesheetData::getXeroEndpoint(), 'DELETE', [$timesheetId, 'Lines', $lineId]);

        XeroRecord::where('xero_id', $lineId)->delete();
    }

    /**
     * @throws XeroValidationException
     */
    protected static function assertTimesheetId(string $timesheetId): void
    {
        if (! preg_match('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}$/iD', $timesheetId)) {
            throw new XeroValidationException('The given timesheet ID is not a valid GUID.');
        }
    }

    protected function gateway(): XeroGateway
    {
        return app(XeroGateway::class);
    }

    /**
     * Identifies the line within its timesheet: one line per date and earnings rate (and tracking item).
     */
    public function lineKey(): string
    {
        $trackingItemID = $this->trackingItemID instanceof Optional ? null : $this->trackingItemID;

        return implode('|', [$this->date->toDateString(), $this->earningsRateID, $trackingItemID]);
    }
}
