<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Dcodegroup\XeroIntegration\Enums\XeroLeavePayoutTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\PayrollAU\LeaveApplication as XeroLeaveApplication;
use XeroPHP\Remote\Model as XeroModel;

class XeroLeaveApplicationData extends AbstractXeroData
{
    use XeroSyncTrait;

    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::LEAVE_APPLICATION_AU;

    protected string $key = '';

    protected array $searchFields = [
        'EmployeeID',
        'LeaveTypeID',
        'StartDate',
        'EndDate',
    ];

    protected array $relatedFields = [
        'LeavePeriods',
    ];

    public function __construct(
        public string $EmployeeID,
        public string $LeaveTypeID,
        public string $Title,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $StartDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $EndDate,
        /** @var XeroLeavePeriodData[]|Collection|Optional|null */
        public Collection|Optional|null $LeavePeriods = null,
        #[WithCast(EnumCast::class)]
        public XeroLeavePayoutTypeEnum|Optional|null $PayOutType = null,
        public string|Optional|null $Description = null,
    ) {}

    public static function fromXero(XeroModel|XeroLeaveApplication $xeroLeaveApplication): self
    {
        return new self(
            EmployeeID: data_get($xeroLeaveApplication, 'EmployeeID'),
            LeaveTypeID: data_get($xeroLeaveApplication, 'LeaveTypeID'),
            Title: data_get($xeroLeaveApplication, 'Title'),
            StartDate: data_get($xeroLeaveApplication, 'StartDate'),
            EndDate: data_get($xeroLeaveApplication, 'EndDate'),
            LeavePeriods: data_get($xeroLeaveApplication, 'LeavePeriods')?->map(fn ($period) => XeroLeavePeriodData::fromXero($period)
            ),
            PayOutType: data_get($xeroLeaveApplication, 'PayOutType'),
            Description: data_get($xeroLeaveApplication, 'Description'),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'EmployeeID' => data_get($this, 'EmployeeID'),
            'LeaveTypeID' => data_get($this, 'LeaveTypeID'),
            'Title' => data_get($this, 'Title'),
            'StartDate' => data_get($this, 'StartDate'),
            'EndDate' => data_get($this, 'EndDate'),
            'LeavePeriods' => data_get($this, 'LeavePeriods') ? XeroLeavePeriodData::toXeroCollection(data_get($this, 'LeavePeriods')) : null,
            'PayOutType' => data_get($this, 'PayOutType')?->toXeroAUValue(),
            'Description' => data_get($this, 'Description'),
        ];
    }
}
