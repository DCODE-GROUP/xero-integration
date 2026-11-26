<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Data\Contracts\HasXeroEndpoint;
use Dcodegroup\XeroIntegration\Data\Contracts\ListsXeroRecords;
use Dcodegroup\XeroIntegration\Data\Traits\ReadsFromXeroApi;
use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;
use LogicException;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use XeroPHP\Remote\Model as XeroModel;

/**
 * Read-only view of a Xero AU pay run.
 */
class XeroPayRunData extends AbstractXeroData implements HasXeroEndpoint, ListsXeroRecords
{
    use ReadsFromXeroApi;

    public function __construct(
        public string $PayRunID,
        public string $PayrollCalendarID,
        public ?string $PayRunStatus = null,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d\TH:i:s', 'Y-m-d'], timeZone: 'UTC')]
        public ?Carbon $PayRunPeriodStartDate = null,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d\TH:i:s', 'Y-m-d'], timeZone: 'UTC')]
        public ?Carbon $PayRunPeriodEndDate = null,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d\TH:i:s', 'Y-m-d'], timeZone: 'UTC')]
        public ?Carbon $PaymentDate = null,
        public ?string $PayslipMessage = null,
        /** @var array<int, array<string, mixed>>|null */
        public ?array $Payslips = null,
        public ?float $Wages = null,
        public ?float $Deductions = null,
        public ?float $Tax = null,
        public ?float $Super = null,
        public ?float $Reimbursement = null,
        public ?float $NetPay = null,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d\TH:i:s', 'Y-m-d'], timeZone: 'UTC', setTimeZone: 'UTC')]
        public ?Carbon $UpdatedDateUTC = null,
    ) {}

    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::PAY_RUN_AU;
    }

    public static function getXeroEndpoint(): XeroEndpoint
    {
        return new XeroEndpoint(
            api: XeroApiEnum::PAYROLL_AU,
            resource: 'PayRuns',
            idKey: 'PayRunID',
            guidFilters: ['PayrollCalendarID', 'PayRunID'],
            paginated: true,
        );
    }

    /**
     * @throws LogicException Pay runs are read through the direct API. Use fromXeroArray().
     */
    public static function fromXero(XeroModel $xeroObject): self
    {
        throw new LogicException('Pay runs are read through the direct API. Use fromXeroArray().');
    }

    /**
     * @throws LogicException Pay runs are read-only.
     */
    public function toXeroArray(): array
    {
        throw new LogicException('Pay runs are read-only and cannot be sent to Xero.');
    }

    public function payrollCalendar(): ?XeroPayrollCalendarData
    {
        return XeroPayrollCalendarData::findCached($this->PayrollCalendarID);
    }
}
