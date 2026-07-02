<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use DcodeGroup\XeroIntegration\Data\XeroContactData;
use DcodeGroup\XeroIntegration\Data\XeroInvoiceItemData;
use DcodeGroup\XeroIntegration\Data\XeroInvoicePaymentData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Invoice as XeroInvoice;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroInvoiceData extends Data implements XeroSyncable
{
    final public function __construct(
        public XeroContactData $Contact,
        public Collection $LineItems,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $InvoiceDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $DueDate,
        public string $InvoiceNumber,
        public string $Status,
        public float $Subtotal,
        public float $TaxAmount,
        public float $Total,
        public float $Discount,
        public Collection|Optional|null $Payments,
        public float $AmountDue,
        public float $AmountPaid,
        public string|Optional|null $InvoiceID,
        public ?Carbon $UpdatedDateUTC,
        public string $Type = XeroInvoice::INVOICE_TYPE_ACCREC,
    ) {}

    /**
     * Create from Xero Model
     * @param XeroInvoice $xeroInvoice
     * @return XeroInvoiceData
     */
    public static function fromXero(XeroModel|XeroInvoice $xeroInvoice): self
    {
        return new static(
            Contact: XeroContactData::fromXero($xeroInvoice->getContact()),
            LineItems: !empty($xeroInvoice->getLineItems()) ?  : collect(),
            InvoiceDate: Carbon::instance($xeroInvoice->getDate()),
            DueDate: Carbon::instance($xeroInvoice->getDueDate()),
            InvoiceNumber: $xeroInvoice->getInvoiceNumber(),
            Status: $xeroInvoice->getStatus(),
            Subtotal: $xeroInvoice->getSubTotal(),
            TaxAmount: $xeroInvoice->getTotalTax(),
            Total: $xeroInvoice->getTotal(),
            Discount: $xeroInvoice->getTotalDiscount(),
            Payments: ! empty($xeroInvoice->getPayments()) ? XeroInvoicePaymentData::xeroCollection($xeroInvoice->getPayments()) : null, // @phpstan-ignore-line
            AmountDue: $xeroInvoice->getAmountDue(),
            AmountPaid: $xeroInvoice->getAmountPaid(),
            InvoiceID: $xeroInvoice->getInvoiceID(),
            UpdatedDateUTC: Carbon::instance($xeroInvoice->getUpdatedDateUTC()),
        );
    }

    public function toXeroArray(): array
    {
        // Ensure we don't overwrite the Contact in Xero if we have a local model with a Xero ID
        // otherwise we may overwrite the Contact details in Xero with outdated information from our local model
        $contactData = $this->Contact->localModel() ? ['ContactID' => $this->Contact->ContactID] : $this->Contact->toXeroArray();

        return [
            'Type' => $this->Type,
            'Contact' => $contactData,
            'LineItems' => !empty($this->LineItems) ? $this->LineItems->map(function (XeroInvoiceItemData $item) {
                return $item->toXeroArray();
            })->toArray() : null,
            'Date' => $this->InvoiceDate,
            'DueDate' => $this->DueDate,
            'InvoiceNumber' => $this->InvoiceNumber,
            'Status' => $this->Status,
            'Subtotal' => $this->Subtotal,
            'TotalTax' => $this->TaxAmount,
            'Total' => $this->Total,
            'TotalDiscount' => $this->Discount,
            'Payments' => ! empty($this->Payments) ? $this->Payments->map(fn ($payment) => $payment->toXeroArray()) : null,
            'AmountDue' => $this->AmountDue,
            'AmountPaid' => $this->AmountPaid,
            'InvoiceID' => $this->InvoiceID,
            'UpdatedDateUTC' => $this->UpdatedDateUTC,
        ];
    }

    abstract public static function fromModel(Model $model): self;

    abstract public function syncToModel(): Model;

    abstract public function localModel(): ?Model;
}
