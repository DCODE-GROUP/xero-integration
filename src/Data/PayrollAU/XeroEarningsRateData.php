<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroAllowanceCategoryEnum;
use Dcodegroup\XeroIntegration\Enums\XeroAllowanceTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroEarningsTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroEmploymentTerminationPaymentTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRateTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\PayrollAU\PayItem\EarningsRate as XeroEarningsRate;
use XeroPHP\Remote\Model as XeroModel;

/**
 * Xero Payroll AU earnings rate. Read-only in the Xero API (retrieved via payroll settings).
 */
class XeroEarningsRateData extends AbstractXeroData
{
    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::EARNINGS_RATE_AU;
    }

    protected string $key = 'EarningsRateID';

    public function __construct(
        public string $Name,
        public ?string $AccountCode,
        public ?string $TypeOfUnits,
        public bool $IsExemptFromTax,
        public bool $IsExemptFromSuper,
        #[WithCast(EnumCast::class)]
        public ?XeroEarningsTypeEnum $EarningsType,
        public string|Optional|null $EarningsRateID = null,
        #[WithCast(EnumCast::class)]
        public XeroRateTypeEnum|Optional|null $RateType = null,
        public bool|Optional|null $IsReportableAsW1 = null,
        public float|Optional|null $RatePerUnit = null,
        public float|Optional|null $Multiplier = null,
        public bool|Optional|null $AccrueLeave = null,
        public float|Optional|null $Amount = null,
        #[WithCast(EnumCast::class)]
        public XeroEmploymentTerminationPaymentTypeEnum|Optional|null $EmploymentTerminationPaymentType = null,
        public bool|Optional|null $CurrentRecord = null,
        #[WithCast(EnumCast::class)]
        public XeroAllowanceTypeEnum|Optional|null $AllowanceType = null,
        #[WithCast(EnumCast::class)]
        public XeroAllowanceCategoryEnum|Optional|null $AllowanceCategory = null,
    ) {}

    public static function fromXero(XeroModel|XeroEarningsRate $xeroEarningsRate): self
    {
        return new self(
            Name: data_get($xeroEarningsRate, 'Name'),
            AccountCode: data_get($xeroEarningsRate, 'AccountCode'),
            TypeOfUnits: data_get($xeroEarningsRate, 'TypeOfUnits'),
            IsExemptFromTax: (bool) data_get($xeroEarningsRate, 'IsExemptFromTax'),
            IsExemptFromSuper: (bool) data_get($xeroEarningsRate, 'IsExemptFromSuper'),
            EarningsType: XeroEarningsTypeEnum::fromXeroAUValue(data_get($xeroEarningsRate, 'EarningsType')),
            EarningsRateID: data_get($xeroEarningsRate, 'EarningsRateID'),
            RateType: XeroRateTypeEnum::fromXeroAUValue(data_get($xeroEarningsRate, 'RateType')),
            IsReportableAsW1: data_get($xeroEarningsRate, 'IsReportableAsW1'),
            RatePerUnit: ($rate = data_get($xeroEarningsRate, 'RatePerUnit')) !== null ? (float) $rate : null,
            Multiplier: ($multiplier = data_get($xeroEarningsRate, 'Multiplier')) !== null ? (float) $multiplier : null,
            AccrueLeave: data_get($xeroEarningsRate, 'AccrueLeave'),
            Amount: ($amount = data_get($xeroEarningsRate, 'Amount')) !== null ? (float) $amount : null,
            EmploymentTerminationPaymentType: XeroEmploymentTerminationPaymentTypeEnum::fromXeroAUValue(data_get($xeroEarningsRate, 'EmploymentTerminationPaymentType')),
            CurrentRecord: data_get($xeroEarningsRate, 'CurrentRecord'),
            AllowanceType: XeroAllowanceTypeEnum::fromXeroAUValue(data_get($xeroEarningsRate, 'AllowanceType')),
            AllowanceCategory: XeroAllowanceCategoryEnum::fromXeroAUValue(data_get($xeroEarningsRate, 'AllowanceCategory')),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'Name' => data_get($this, 'Name'),
            'AccountCode' => data_get($this, 'AccountCode'),
            'TypeOfUnits' => data_get($this, 'TypeOfUnits'),
            'IsExemptFromTax' => data_get($this, 'IsExemptFromTax'),
            'IsExemptFromSuper' => data_get($this, 'IsExemptFromSuper'),
            'EarningsType' => data_get($this, 'EarningsType')?->toXeroAUValue(),
            'EarningsRateID' => data_get($this, 'EarningsRateID'),
            'RateType' => data_get($this, 'RateType')?->toXeroAUValue(),
            'IsReportableAsW1' => data_get($this, 'IsReportableAsW1'),
            'RatePerUnit' => data_get($this, 'RatePerUnit'),
            'Multiplier' => data_get($this, 'Multiplier'),
            'AccrueLeave' => data_get($this, 'AccrueLeave'),
            'Amount' => data_get($this, 'Amount'),
            'EmploymentTerminationPaymentType' => data_get($this, 'EmploymentTerminationPaymentType')?->toXeroAUValue(),
            'CurrentRecord' => data_get($this, 'CurrentRecord'),
            'AllowanceType' => data_get($this, 'AllowanceType')?->toXeroAUValue(),
            'AllowanceCategory' => data_get($this, 'AllowanceCategory')?->toXeroAUValue(),
        ];
    }
}
