<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Invoice as XeroInvoice;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroInvoiceData extends AbstractXeroData
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
        public string|Optional|null $LineAmountTypes = null,
        public string|Optional|null $Reference = null,
        public string|Optional|null $BrandingThemeID = null,
        public string|Optional|null $Url = null,
        public string|Optional|null $CurrencyCode = null,
        public float|Optional|null $CurrencyRate = null,
        public bool|Optional|null $SentToContact = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $ExpectedPaymentDate = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $PlannedPaymentDate = null,
        public string|Optional|null $RepeatingInvoiceID = null,
        public bool|Optional|null $HasAttachments = null,
        /** @var Collection<int,XeroPrepaymentData>|null */
        public Collection|Optional|null $Prepayments = null,
        /** @var Collection<int,XeroOverpaymentData>|null */
        public Collection|Optional|null $Overpayments = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $FullyPaidOnDate = null,
        public float|Optional|null $AmountCredited = null,
        /** @var Collection<int,XeroCreditNoteData>|null */
        public Collection|Optional|null $CreditNotes = null,
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
            Type: data_get($xeroInvoice, 'Type', XeroInvoice::INVOICE_TYPE_ACCREC),
            LineAmountTypes: data_get($xeroInvoice, 'LineAmountTypes'),
            Reference: data_get($xeroInvoice, 'Reference'),
            BrandingThemeID: data_get($xeroInvoice, 'BrandingThemeID'),
            Url: data_get($xeroInvoice, 'Url'),
            CurrencyCode: data_get($xeroInvoice, 'CurrencyCode'),
            CurrencyRate: data_get($xeroInvoice, 'CurrencyRate'),
            SentToContact: data_get($xeroInvoice, 'SentToContact'),
            ExpectedPaymentDate: data_get($xeroInvoice, 'ExpectedPaymentDate') ? Carbon::instance(data_get($xeroInvoice, 'ExpectedPaymentDate')) : null,
            PlannedPaymentDate: data_get($xeroInvoice, 'PlannedPaymentDate') ? Carbon::instance(data_get($xeroInvoice, 'PlannedPaymentDate')) : null,
            RepeatingInvoiceID: data_get($xeroInvoice, 'RepeatingInvoiceID'),
            HasAttachments: data_get($xeroInvoice, 'HasAttachments'),
            Prepayments: XeroPrepaymentData::toCollection(data_get($xeroInvoice, 'Prepayments')),
            Overpayments: XeroOverpaymentData::toCollection(data_get($xeroInvoice, 'Overpayments')),
            FullyPaidOnDate: data_get($xeroInvoice, 'FullyPaidOnDate') ? Carbon::instance(data_get($xeroInvoice, 'FullyPaidOnDate')) : null,
            AmountCredited: data_get($xeroInvoice, 'AmountCredited'),
            CreditNotes: XeroCreditNoteData::toCollection(data_get($xeroInvoice, 'CreditNotes')),
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
            'LineAmountTypes' => data_get($this, 'LineAmountTypes'),
            'Reference' => data_get($this, 'Reference'),
            'BrandingThemeID' => data_get($this, 'BrandingThemeID'),
            'Url' => data_get($this, 'Url'),
            'CurrencyCode' => data_get($this, 'CurrencyCode'),
            'CurrencyRate' => data_get($this, 'CurrencyRate'),
            'SentToContact' => data_get($this, 'SentToContact'),
            'ExpectedPaymentDate' => data_get($this, 'ExpectedPaymentDate'),
            'PlannedPaymentDate' => data_get($this, 'PlannedPaymentDate'),
            'RepeatingInvoiceID' => data_get($this, 'RepeatingInvoiceID'),
            'HasAttachments' => data_get($this, 'HasAttachments'),
            'Prepayments' => XeroPrepaymentData::toXeroCollection(data_get($this, 'Prepayments')),
            'Overpayments' => XeroOverpaymentData::toXeroCollection(data_get($this, 'Overpayments')),
            'FullyPaidOnDate' => data_get($this, 'FullyPaidOnDate'),
            'AmountCredited' => data_get($this, 'AmountCredited'),
            'CreditNotes' => XeroCreditNoteData::toXeroCollection(data_get($this, 'CreditNotes')),
        ];
    }
}
