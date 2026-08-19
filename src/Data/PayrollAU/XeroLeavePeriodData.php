<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroLeavePeriodStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\PayrollAU\LeaveApplication\LeavePeriod as XeroLeavePeriod;
use XeroPHP\Remote\Model as XeroModel;

class XeroLeavePeriodData extends AbstractXeroData
{
    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::LEAVE_APPLICATION_AU;

    protected array $searchFields = [
        'NumberOfUnits',
        'PayPeriodEndDate',
        'PayPeriodStartDate',
        'LeavePeriodStatus',
    ];

    protected array $relatedFields = [];

    public function __construct(
        public float $NumberOfUnits,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $PayPeriodEndDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $PayPeriodStartDate = null,
        #[WithCast(EnumCast::class)]
        public XeroLeavePeriodStatusEnum|Optional|null $LeavePeriodStatus = null,
    ) {}

    public static function fromXero(XeroModel|XeroLeavePeriod $xeroLeavePeriod): self
    {
        return new self(
            NumberOfUnits: (float) data_get($xeroLeavePeriod, 'NumberOfUnits'),
            PayPeriodEndDate: data_get($xeroLeavePeriod, 'PayPeriodEndDate'),
            PayPeriodStartDate: data_get($xeroLeavePeriod, 'PayPeriodStartDate'),
            LeavePeriodStatus: data_get($xeroLeavePeriod, 'LeavePeriodStatus'),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'NumberOfUnits' => data_get($this, 'NumberOfUnits'),
            'PayPeriodEndDate' => data_get($this, 'PayPeriodEndDate'),
            'PayPeriodStartDate' => data_get($this, 'PayPeriodStartDate'),
            'LeavePeriodStatus' => data_get($this, 'LeavePeriodStatus')?->toXeroAUValue(),
        ];
    }
}
