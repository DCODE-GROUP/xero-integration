<?php

namespace DcodeGroup\XeroIntegration\Data;


use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\LineItem;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroInvoiceItemData extends Data implements XeroSyncable
{
    final public function __construct(
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
            'LineItemID' => $this->LineItemID,
            'Description' => $this->Description,
            'Quantity' => $this->Quantity,
            'UnitAmount' => $this->UnitAmount,
            'LineAmount' => $this->LineAmount,
            'TaxAmount' => $this->TaxAmount,
            'DiscountAmount' => $this->DiscountAmount,
        ];
    }

    /**
     * Create from Xero Model
     * @param LineItem $xeroLineItem
     * @return self
     */
    public static function fromXero(XeroModel|LineItem $xeroLineItem): self
    {
        return new static(
            LineItemID: $xeroLineItem->getLineItemID(),
            Description: $xeroLineItem->getDescription(),
            Quantity: (float) $xeroLineItem->getQuantity(),
            UnitAmount: $xeroLineItem->getUnitAmount(),
            LineAmount: $xeroLineItem->getLineAmount(),
            TaxAmount: $xeroLineItem->getTaxAmount(),
            DiscountAmount: $xeroLineItem->getDiscountAmount(),
        );
    }

    abstract public static function fromModel(Model $model): self;

    abstract public function syncToModel(): Model;

    abstract public function localModel(): ?Model;
}
