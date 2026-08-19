<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Enums\XeroTimesheetStatusEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\PayrollAU\Timesheet as XeroTimesheet;
use XeroPHP\Remote\Model as XeroModel;

class XeroTimesheetData extends AbstractXeroData
{
    use XeroSyncTrait;

    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::TIMESHEET_AU;

    protected string $key = 'TimesheetID';

    protected array $searchFields = [
        'EmployeeID',
        'StartDate',
        'EndDate',
    ];

    protected array $relatedFields = [
        'TimesheetLines',
    ];

    public function __construct(
        public string $EmployeeID,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $StartDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $EndDate,
        /** @var XeroTimesheetLineData[]|Collection */
        public Collection $TimesheetLines,
        public string|Optional|null $TimesheetID = null,
        #[WithCast(EnumCast::class)]
        public XeroTimesheetStatusEnum|Optional|null $Status = null,
        public int|Optional|null $Hours = null,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon|Optional|null $UpdatedDateUTC = null,
    ) {}

    public static function fromXero(XeroModel|XeroTimesheet $xeroTimesheet): self
    {
        return new self(
            EmployeeID: data_get($xeroTimesheet, 'EmployeeID'),
            StartDate: data_get($xeroTimesheet, 'StartDate'),
            EndDate: data_get($xeroTimesheet, 'EndDate'),
            TimesheetLines: data_get($xeroTimesheet, 'TimesheetLines')->map(fn ($line) => XeroTimesheetLineData::fromXero($line)
            ),
            TimesheetID: data_get($xeroTimesheet, 'TimesheetID'),
            Status: data_get($xeroTimesheet, 'Status'),
            Hours: data_get($xeroTimesheet, 'Hours'),
            UpdatedDateUTC: data_get($xeroTimesheet, 'UpdatedDateUTC'),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'EmployeeID' => data_get($this, 'EmployeeID'),
            'StartDate' => data_get($this, 'StartDate'),
            'EndDate' => data_get($this, 'EndDate'),
            'TimesheetLines' => XeroTimesheetLineData::toXeroCollection(data_get($this, 'TimesheetLines')),
            'TimesheetID' => data_get($this, 'TimesheetID'),
            'Status' => data_get($this, 'Status')?->toXeroAUValue(),
            'Hours' => data_get($this, 'Hours'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }
}
