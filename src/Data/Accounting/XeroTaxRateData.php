<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\TaxRate as XeroTaxRate;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroTaxRateData extends AbstractXeroData
{
    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::TAX_RATE;

    protected array $searchFields = [
        'Name',
    ];

    protected array $relatedFields = [
        'TaxComponents',
    ];

    public function __construct(
        public string|Optional|null $Name,
        public string|Optional|null $TaxType,
        /** @var Collection<int,XeroTaxComponentData> */
        public Collection $TaxComponents,
        public string|Optional|null $Status = null,
        public string|Optional|null $ReportTaxTypes = null,
        public bool|Optional|null $CanApplyToAssets = null,
        public bool|Optional|null $CanApplyToEquity = null,
        public bool|Optional|null $CanApplyToExpenses = null,
        public bool|Optional|null $CanApplyToLiabilities = null,
        public bool|Optional|null $CanApplyToRevenue = null,
        public string|Optional|null $DisplayTaxRate = null,
        public float|Optional|null $EffectiveRate = null,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'Name' => data_get($this, 'Name'),
            'TaxType' => data_get($this, 'TaxType'),
            'TaxComponents' => data_get($this, 'TaxComponents'),
            'Status' => data_get($this, 'Status'),
            'ReportTaxTypes' => data_get($this, 'ReportTaxTypes'),
            'CanApplyToAssets' => data_get($this, 'CanApplyToAssets'),
            'CanApplyToEquity' => data_get($this, 'CanApplyToEquity'),
            'CanApplyToExpenses' => data_get($this, 'CanApplyToExpenses'),
            'CanApplyToLiabilities' => data_get($this, 'CanApplyToLiabilities'),
            'CanApplyToRevenue' => data_get($this, 'CanApplyToRevenue'),
            'DisplayTaxRate' => data_get($this, 'DisplayTaxRate'),
            'EffectiveRate' => data_get($this, 'EffectiveRate'),
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  XeroTaxRate  $xeroTaxRate
     */
    public static function fromXero(XeroModel|XeroTaxRate $xeroTaxRate): self
    {
        return new static(
            Name: data_get($xeroTaxRate, 'Name'),
            TaxType: data_get($xeroTaxRate, 'TaxType'),
            TaxComponents: data_get($xeroTaxRate, 'TaxComponents'),
            Status: data_get($xeroTaxRate, 'Status'),
            ReportTaxTypes: data_get($xeroTaxRate, 'ReportTaxTypes'),
            CanApplyToAssets: data_get($xeroTaxRate, 'CanApplyToAssets'),
            CanApplyToEquity: data_get($xeroTaxRate, 'CanApplyToEquity'),
            CanApplyToExpenses: data_get($xeroTaxRate, 'CanApplyToExpenses'),
            CanApplyToLiabilities: data_get($xeroTaxRate, 'CanApplyToLiabilities'),
            CanApplyToRevenue: data_get($xeroTaxRate, 'CanApplyToRevenue'),
            DisplayTaxRate: data_get($xeroTaxRate, 'DisplayTaxRate'),
            EffectiveRate: data_get($xeroTaxRate, 'EffectiveRate')
        );
    }
}
