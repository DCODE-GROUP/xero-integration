<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Address as XeroAddress;
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
     * @param  XeroAddress  $xeroAddress
     */
    public static function fromXero(XeroModel|XeroAddress $xeroAddress): self
    {
        return new static(
            Name: data_get($xeroAddress, 'Name'),
            TaxType: data_get($xeroAddress, 'TaxType'),
            TaxComponents: data_get($xeroAddress, 'TaxComponents'),
            Status: data_get($xeroAddress, 'Status'),
            ReportTaxTypes: data_get($xeroAddress, 'ReportTaxTypes'),
            CanApplyToAssets: data_get($xeroAddress, 'CanApplyToAssets'),
            CanApplyToEquity: data_get($xeroAddress, 'CanApplyToEquity'),
            CanApplyToExpenses: data_get($xeroAddress, 'CanApplyToExpenses'),
            CanApplyToLiabilities: data_get($xeroAddress, 'CanApplyToLiabilities'),
            CanApplyToRevenue: data_get($xeroAddress, 'CanApplyToRevenue'),
            DisplayTaxRate: data_get($this, 'DisplayTaxRate'),
            EffectiveRate: data_get($this, 'EffectiveRate')
        );
    }
}
