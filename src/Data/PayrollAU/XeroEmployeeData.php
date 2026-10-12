<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Data\Accounting\XeroAddressData;
use Dcodegroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\XeroApp;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use XeroPHP\Models\PayrollAU\Employee as XeroEmployee;
use XeroPHP\Remote\Model as XeroModel;

class XeroEmployeeData extends AbstractXeroData
{
    use XeroSyncTrait;

    public ?string $key = 'EmployeeID';

    public ?array $relatedFields = [];

    public function __construct(
        public string $FirstName,
        public string $LastName,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $DateOfBirth,
        public ?XeroAddressData $HomeAddress,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $StartDate,
        public ?string $Title,
        public ?string $MiddleNames,
        public ?string $Email,
        public ?string $Gender,
        public ?string $Phone,
        public ?string $Mobile,
        public ?string $TwitterUserName,
        public ?bool $IsAuthorisedToApproveLeave,
        public ?bool $IsAuthorisedToApproveTimesheets,
        public ?string $JobTitle,
        public ?string $Classification,
        public ?string $OrdinaryEarningsRateID,
        public ?string $PayrollCalendarID,
        public ?string $EmployeeGroupName,
        public ?array $BankAccounts,
        public ?array $PayTemplate,
        public ?array $OpeningBalances,
        public ?array $LeaveBalances,
        public ?array $SuperMemberships,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $TerminationDate,
        public ?string $TerminationReason,
        public ?string $EmployeeID,
        public ?string $Status,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon $UpdatedDateUTC,
        public ?bool $IsSTP2Qualified,
        public ?array $IncomeType,
        public ?array $EmploymentType,
        public ?string $CountryOfResidence
    ) {}

    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::TIMESHEET_AU;
    }

    public function toXeroArray(): array
    {
        throw new \Exception('Not implemented');
    }

    public static function fromXero(XeroModel|XeroEmployee $xeroEmployee): self
    {
        return static::from($xeroEmployee->toStringArray());
    }

    public static function list()
    {
        return static::query()->execute();
    }

    public static function find(string $guid)
    {
        $class = static::query()->getFrom();
        $result = app(XeroApp::class)->loadByGUID($class, $guid);
        if ($result) {
            return static::fromXero($result);
        } else {
            return null;
        }
    }
}
