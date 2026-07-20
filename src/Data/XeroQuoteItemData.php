<?php

namespace DcodeGroup\XeroIntegration\Data;

use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\LineItem as XeroLineItem;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
abstract class XeroQuoteItemData extends AbstractXeroData
{
    protected string $xeroRelationship = 'line-item';

    protected array $searchFields = [
        'Description',
        'Quantity',
        'UnitAmount',
        'LineAmount',
    ];

    protected array $relatedFields = [];

    public function __construct(
        public string|Optional|null $LineItemID,
        public string $Description,
        public float $Quantity,
        public float|Optional|null $UnitAmount,
        public float $LineAmount,
        public string|Optional|null $ItemCode,
        public string|Optional|null $AccountCode,
        public string|Optional|null $AccountId,
        public string|Optional|null $TaxType,
        public float|Optional|null $TaxAmount,
        public string|Optional|null $DiscountRate,
        public float|Optional|null $DiscountAmount,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'LineItemID' => data_get($this, 'LineItemID'),
            'Description' => data_get($this, 'Description'),
            'Quantity' => data_get($this, 'Quantity'),
            'UnitAmount' => data_get($this, 'UnitAmount'),
            'LineAmount' => data_get($this, 'LineAmount'),
            'ItemCode' => data_get($this, 'ItemCode'),
            'AccountCode' => data_get($this, 'AccountCode'),
            'AccountId' => data_get($this, 'AccountId'),
            'TaxType' => data_get($this, 'TaxType'),
            'TaxAmount' => data_get($this, 'TaxAmount'),
            'DiscountRate' => data_get($this, 'DiscountRate'),
            'DiscountAmount' => data_get($this, 'DiscountAmount'),
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  XeroLineItem  $xeroQuoteItem
     */
    protected static function fromXero(XeroModel|XeroLineItem $xeroQuoteItem): self
    {
        return new static(
            LineItemID: data_get($xeroQuoteItem, 'LineItemID'),
            Description: data_get($xeroQuoteItem, 'Description'),
            Quantity: (float) data_get($xeroQuoteItem, 'Quantity'),
            UnitAmount: (float) data_get($xeroQuoteItem, 'UnitAmount'),
            LineAmount: (float) data_get($xeroQuoteItem, 'LineAmount'),
            ItemCode: data_get($xeroQuoteItem, 'ItemCode'),
            AccountCode: data_get($xeroQuoteItem, 'AccountCode'),
            AccountId: data_get($xeroQuoteItem, 'AccountId'),
            TaxType: data_get($xeroQuoteItem, 'TaxType'),
            TaxAmount: (float) data_get($xeroQuoteItem, 'TaxAmount'),
            DiscountRate: data_get($xeroQuoteItem, 'DiscountRate'),
            DiscountAmount: (float) data_get($xeroQuoteItem, 'DiscountAmount'),
        );
    }
}
