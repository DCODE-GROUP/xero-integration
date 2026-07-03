<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\HasXeroData;
use DcodeGroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Invoice as XeroInvoice;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroInvoiceData extends AbstractXeroData implements HasXeroData
{
    use XeroSyncTrait;

    protected string $xeroRelationship = 'invoice';

    protected array $searchFields = [
        'InvoiceNumber',
    ];

    protected array $relatedFields = [
        'Contact',
        'LineItems',
        'Payments',
    ];

    final public function __construct(
        public string|Optional|null $InvoiceID,
        public XeroContactData $Contact,
        /** @var Collection<int,XeroInvoiceItemData> */
        public Collection $LineItems,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $InvoiceDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $DueDate,
        public string $InvoiceNumber,
        public string $Status, // ToDo: change to Enum
        public float $Subtotal,
        public float $TaxAmount,
        public float $Total,
        public float $Discount,
        /** @var Collection<int,XeroPaymentData>|null */
        public Collection|Optional|null $Payments,
        public float $AmountDue,
        public float $AmountPaid,
        public ?Carbon $UpdatedDateUTC,
        public string $Type = XeroInvoice::INVOICE_TYPE_ACCREC, // ToDo: change to Enum
    ) {}

    /**
     * Create from Xero Model
     *
     * @param  XeroInvoice  $xeroInvoice
     */
    protected static function fromXero(XeroModel|XeroInvoice $xeroInvoice): self
    {
        return new static(
            InvoiceID: data_get($xeroInvoice, 'InvoiceID'),
            Contact: XeroContactData::fromXero(data_get($xeroInvoice, 'Contact')),
            LineItems: XeroInvoiceItemData::toCollection(data_get($xeroInvoice, 'LineItems')),
            InvoiceDate: Carbon::instance(data_get($xeroInvoice, 'Date')),
            DueDate: Carbon::instance(data_get($xeroInvoice, 'DueDate')),
            InvoiceNumber: data_get($xeroInvoice, 'InvoiceNumber'),
            Status: data_get($xeroInvoice, 'Status'),
            Subtotal: data_get($xeroInvoice, 'SubTotal'),
            TaxAmount: data_get($xeroInvoice, 'TotalTax'),
            Total: data_get($xeroInvoice, 'Total'),
            Discount: data_get($xeroInvoice, 'TotalDiscount'),
            Payments: XeroPaymentData::toCollection(data_get($xeroInvoice, 'Payments')),
            AmountDue: data_get($xeroInvoice, 'AmountDue'),
            AmountPaid: data_get($xeroInvoice, 'AmountPaid'),
            UpdatedDateUTC: Carbon::instance(data_get($xeroInvoice, 'UpdatedDateUTC')),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'Type' => data_get($this, 'Type'),
            'Contact' => data_get($this, 'Contact')?->toXeroArray(),
            'LineItems' => XeroInvoiceItemData::toXeroCollection(data_get($this, 'LineItems')),
            'Date' => data_get($this, 'InvoiceDate'),
            'DueDate' => data_get($this, 'DueDate'),
            'InvoiceNumber' => data_get($this, 'InvoiceNumber'),
            'Status' => data_get($this, 'Status'),
            'Subtotal' => data_get($this, 'Subtotal'),
            'TotalTax' => data_get($this, 'TaxAmount'),
            'Total' => data_get($this, 'Total'),
            'TotalDiscount' => data_get($this, 'Discount'),
            'Payments' => XeroPaymentData::toXeroCollection(data_get($this, 'Payments')),
            'AmountDue' => data_get($this, 'AmountDue'),
            'AmountPaid' => data_get($this, 'AmountPaid'),
            'InvoiceID' => data_get($this, 'InvoiceID'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }
}
