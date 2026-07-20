<?php

namespace DcodeGroup\XeroIntegration\Data;

use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\LineItem;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
abstract class XeroInvoiceItemData extends AbstractXeroData
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
        public float|Optional|null $TaxAmount,
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
            'TaxAmount' => data_get($this, 'TaxAmount'),
            'DiscountAmount' => data_get($this, 'DiscountAmount'),
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
        );
    }
}
