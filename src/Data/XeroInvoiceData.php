<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use DcodeGroup\XeroIntegration\Enums\XeroInvoiceStatusEnum;
use DcodeGroup\XeroIntegration\Enums\XeroInvoiceTypeEnum;
use DcodeGroup\XeroIntegration\Enums\XeroLineAmountTypeEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Invoice as XeroInvoice;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
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

    public function __construct(
        public string|Optional|null $InvoiceID,
        public XeroContactData $Contact,
        /** @var Collection<int,XeroItemData> */
        public Collection $LineItems,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $InvoiceDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $DueDate,
        public string $InvoiceNumber,
        public XeroInvoiceStatusEnum $Status,
        public float $Subtotal,
        public float $TaxAmount,
        public float $Total,
        public float|Optional|null $TotalDiscount,
        /** @var Collection<int,XeroPaymentData>|null */
        public Collection|Optional|null $Payments,
        public float $AmountDue,
        public float $AmountPaid,
        public ?Carbon $UpdatedDateUTC,
        public XeroInvoiceTypeEnum $Type,
        public XeroLineAmountTypeEnum|Optional|null $LineAmountTypes,
        public string|Optional|null $Reference,
        public string|Optional|null $BrandingThemeID,
        public string|Optional|null $Url,
        public string|Optional|null $CurrencyCode,
        public float|Optional|null $CurrencyRate,
        public bool|Optional|null $SentToContact,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $ExpectedPaymentDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $PlannedPaymentDate,
        public string|Optional|null $RepeatingInvoiceID,
        public bool|Optional|null $HasAttachments,
        /** @var Collection<int,XeroPrepaymentData>|null */
        public Collection|Optional|null $Prepayments,
        /** @var Collection<int,XeroOverpaymentData>|null */
        public Collection|Optional|null $Overpayments,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $FullyPaidOnDate,
        public float|Optional|null $AmountCredited,
        /** @var Collection<int,XeroCreditNoteData>|null */
        public Collection|Optional|null $CreditNotes,
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
            LineItems: XeroItemData::toCollection(data_get($xeroInvoice, 'LineItems')),
            InvoiceDate: Carbon::instance(data_get($xeroInvoice, 'Date')),
            DueDate: Carbon::instance(data_get($xeroInvoice, 'DueDate')),
            InvoiceNumber: data_get($xeroInvoice, 'InvoiceNumber'),
            Status: XeroInvoiceStatusEnum::TryFrom(data_get($xeroInvoice, 'Status')),
            Subtotal: data_get($xeroInvoice, 'SubTotal'),
            TaxAmount: data_get($xeroInvoice, 'TotalTax'),
            Total: data_get($xeroInvoice, 'Total'),
            TotalDiscount: data_get($xeroInvoice, 'TotalDiscount'),
            Payments: XeroPaymentData::toCollection(data_get($xeroInvoice, 'Payments')),
            AmountDue: data_get($xeroInvoice, 'AmountDue'),
            AmountPaid: data_get($xeroInvoice, 'AmountPaid'),
            UpdatedDateUTC: Carbon::instance(data_get($xeroInvoice, 'UpdatedDateUTC')),
            Type: XeroInvoiceTypeEnum::TryFrom(data_get($xeroInvoice, 'Type', XeroInvoice::INVOICE_TYPE_ACCREC)),
            LineAmountTypes: XeroLineAmountTypeEnum::TryFrom(data_get($xeroInvoice, 'LineAmountTypes')),
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
            'Type' => data_get($this, 'Type')?->getXeroValue(),
            'Contact' => data_get($this, 'Contact')?->toXeroArray(),
            'LineItems' => XeroItemData::toXeroCollection(data_get($this, 'LineItems')),
            'Date' => data_get($this, 'InvoiceDate'),
            'DueDate' => data_get($this, 'DueDate'),
            'InvoiceNumber' => data_get($this, 'InvoiceNumber'),
            'Status' => data_get($this, 'Status')?->getXeroValue(),
            'Subtotal' => data_get($this, 'Subtotal'),
            'TotalTax' => data_get($this, 'TaxAmount'),
            'Total' => data_get($this, 'Total'),
            'TotalDiscount' => data_get($this, 'TotalDiscount'),
            'Payments' => XeroPaymentData::toXeroCollection(data_get($this, 'Payments')),
            'AmountDue' => data_get($this, 'AmountDue'),
            'AmountPaid' => data_get($this, 'AmountPaid'),
            'InvoiceID' => data_get($this, 'InvoiceID'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
            'LineAmountTypes' => data_get($this, 'LineAmountTypes')?->getXeroValue(),
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
