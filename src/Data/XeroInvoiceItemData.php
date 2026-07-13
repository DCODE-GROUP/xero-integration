<?php

namespace DcodeGroup\XeroIntegration\Data;

use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\LineItem;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroInvoiceItemData extends AbstractXeroData
{
    protected string $xeroRelationship = 'line-item';

    protected array $searchFields = [
        'Description',
        'Quantity',
        'UnitAmount',
        'LineAmount',
        'ItemCode',
        'AccountCode',
    ];

    protected array $relatedFields = [];

    final public function __construct(
        public string|Optional|null $LineItemID,
        public string $Description,
        public float $Quantity,
        public float|Optional|null $UnitAmount,
        public float $LineAmount,
        public float|Optional|null $TaxAmount,
        public float|Optional|null $DiscountAmount,
        public string|Optional|null $ItemCode = null,
        public string|Optional|null $AccountCode = null,
        public string|Optional|null $AccountId = null,
        public string|Optional|null $TaxType = null,
        public string|Optional|null $DiscountRate = null,
        public array|Optional|null $Tracking = null,
        public string|Optional|null $RepeatingInvoiceID = null,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'LineItemID' => data_get($this, 'LineItemID'),
            'Description' => data_get($this, 'Description'),
            'Quantity' => data_get($this, 'Quantity'),
            'UnitAmount' => data_get($this, 'UnitAmount'),
            'LineAmount' => data_get($this, 'LineAmount'),
            'TaxAmount' => data_get($this, 'TaxAmount'),
            'DiscountAmount' => data_get($this, 'DiscountAmount'),
            'ItemCode' => data_get($this, 'ItemCode'),
            'AccountCode' => data_get($this, 'AccountCode'),
            'AccountId' => data_get($this, 'AccountId'),
            'TaxType' => data_get($this, 'TaxType'),
            'DiscountRate' => data_get($this, 'DiscountRate'),
            'Tracking' => data_get($this, 'Tracking'),
            'RepeatingInvoiceID' => data_get($this, 'RepeatingInvoiceID'),
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  LineItem  $xeroLineItem
     */
    protected static function fromXero(XeroModel|LineItem $xeroLineItem): self
    {
        return new static(
            LineItemID: data_get($xeroLineItem, 'LineItemID'),
            Description: data_get($xeroLineItem, 'Description'),
            Quantity: (float) data_get($xeroLineItem, 'Quantity'),
            UnitAmount: data_get($xeroLineItem, 'UnitAmount'),
            LineAmount: data_get($xeroLineItem, 'LineAmount'),
            TaxAmount: data_get($xeroLineItem, 'TaxAmount'),
            DiscountAmount: data_get($xeroLineItem, 'DiscountAmount'),
            ItemCode: data_get($xeroLineItem, 'ItemCode'),
            AccountCode: data_get($xeroLineItem, 'AccountCode'),
            AccountId: data_get($xeroLineItem, 'AccountId'),
            TaxType: data_get($xeroLineItem, 'TaxType'),
            DiscountRate: data_get($xeroLineItem, 'DiscountRate'),
            Tracking: data_get($xeroLineItem, 'Tracking'),
            RepeatingInvoiceID: data_get($xeroLineItem, 'RepeatingInvoiceID'),
        );
    }
}
