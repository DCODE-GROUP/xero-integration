<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use LogicException;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Remote\Model as XeroModel;

/**
 * A Payroll AU 2.0 timesheet line: the units worked on one date on one earnings rate.
 */
class XeroTimesheetLineData extends AbstractXeroData
{
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
     * Identifies the line within its timesheet: one line per date and earnings rate (and tracking item).
     */
    public function lineKey(): string
    {
        $trackingItemID = $this->trackingItemID instanceof Optional ? null : $this->trackingItemID;

        return implode('|', [$this->date->toDateString(), $this->earningsRateID, $trackingItemID]);
    }
}
