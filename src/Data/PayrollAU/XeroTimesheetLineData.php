<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\PayrollAU\Timesheet as XeroTimesheet;
use XeroPHP\Remote\Model as XeroModel;

class XeroTimesheetLineData extends AbstractXeroData
{
    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::TIMESHEET_LINE_AU;

    protected array $searchFields = [
        'EarningsRateID',
        'NumberOfUnits',
    ];

    protected array $relatedFields = [];

    public function __construct(
        public string|Optional|null $EarningsRateID = null,
        /** @var float[]|Optional|null */
        public array|Optional|null $NumberOfUnits = null,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon|Optional|null $UpdatedDateUTC = null,
    ) {}

    public static function fromXero(XeroModel|XeroTimesheet $xeroTimesheet): self
    {
        return new self(
            EarningsRateID: data_get($xeroTimesheet, 'EarningsRateID'),
            NumberOfUnits: data_get($xeroTimesheet, 'NumberOfUnits'),
            UpdatedDateUTC: data_get($xeroTimesheet, 'UpdatedDateUTC'),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'EarningsRateID' => data_get($this, 'EarningsRateID'),
            'NumberOfUnits' => data_get($this, 'NumberOfUnits'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }
}
