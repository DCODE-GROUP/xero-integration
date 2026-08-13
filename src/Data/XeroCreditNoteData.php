<?php

namespace Dcodegroup\XeroIntegration\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Dcodegroup\XeroIntegration\Enums\XeroCreditNoteTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroInvoiceStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroLineAmountTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\CreditNote as XeroCreditNote;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroCreditNoteData extends AbstractXeroData
{
    use XeroSyncTrait;

    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::CREDIT_NOTE;

    protected string $key = 'CreditNoteID';

    protected array $searchFields = [
        'CreditNoteID',
        'CreditNoteNumber',
        'Status',
    ];

    protected array $relatedFields = [
        'Contact',
        'LineItems',
    ];

    public function __construct(
        public string|Optional|null $CreditNoteID = null,
        public XeroContactData $Contact,
        /** @var Collection<int,XeroItemData> */
        public Collection $LineItems,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        public XeroInvoiceStatusEnum $Status,
        public float $SubTotal,
        public float $TotalTax,
        public float $Total,
        public XeroCreditNoteTypeEnum $Type,
        public string|Optional|null $CreditNoteNumber = null,
        public XeroLineAmountTypeEnum|Optional|null $LineAmountTypes = null,
        public string|Optional|null $Reference = null,
        public bool|Optional|null $SentToContact = null,
        public string|Optional|null $CurrencyCode = null,
        public float|Optional|null $CurrencyRate = null,
        public string|Optional|null $RemainingCredit = null,
        public string|Optional|null $BrandingThemeID = null,
        public bool|Optional|null $HasAttachments = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $FullyPaidOnDate = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $UpdatedDateUTC = null,
    ) {}

    /**
     * Create from Xero Model
     */
    public static function fromXero(XeroModel|XeroCreditNote $xeroCreditNote): self
    {
        return new static(
            CreditNoteID: data_get($xeroCreditNote, 'CreditNoteID'),
            Contact: XeroContactData::fromXero(data_get($xeroCreditNote, 'Contact')),
            LineItems: XeroItemData::toCollection(data_get($xeroCreditNote, 'LineItems')),
            Date: Carbon::instance(data_get($xeroCreditNote, 'Date')),
            Status: XeroInvoiceStatusEnum::TryFrom(data_get($xeroCreditNote, 'Status')),
            SubTotal: data_get($xeroCreditNote, 'SubTotal'),
            TotalTax: data_get($xeroCreditNote, 'TotalTax'),
            Total: data_get($xeroCreditNote, 'Total'),
            Type: XeroCreditNoteTypeEnum::TryFrom(data_get($xeroCreditNote, 'Type')),
            CreditNoteNumber: data_get($xeroCreditNote, 'CreditNoteNumber'),
            LineAmountTypes: XeroLineAmountTypeEnum::TryFrom(data_get($xeroCreditNote, 'LineAmountTypes')),
            Reference: data_get($xeroCreditNote, 'Reference'),
            SentToContact: data_get($xeroCreditNote, 'SentToContact'),
            CurrencyCode: data_get($xeroCreditNote, 'CurrencyCode'),
            CurrencyRate: data_get($xeroCreditNote, 'CurrencyRate'),
            RemainingCredit: data_get($xeroCreditNote, 'RemainingCredit'),
            BrandingThemeID: data_get($xeroCreditNote, 'BrandingThemeID'),
            HasAttachments: data_get($xeroCreditNote, 'HasAttachments'),
            FullyPaidOnDate: data_get($xeroCreditNote, 'FullyPaidOnDate') ? Carbon::instance(data_get($xeroCreditNote, 'FullyPaidOnDate')) : null,
            UpdatedDateUTC: data_get($xeroCreditNote, 'UpdatedDateUTC') ? Carbon::instance(data_get($xeroCreditNote, 'UpdatedDateUTC')) : null,
        );
    }

    public function toXeroArray(): array
    {
        return [
            'CreditNoteID' => data_get($this, 'CreditNoteID'),
            'Contact' => data_get($this, 'Contact')?->toXeroArray(),
            'LineItems' => XeroItemData::toXeroCollection(data_get($this, 'LineItems')),
            'Date' => data_get($this, 'Date'),
            'Status' => data_get($this, 'Status')?->getXeroValue(),
            'SubTotal' => data_get($this, 'SubTotal'),
            'TotalTax' => data_get($this, 'TotalTax'),
            'Total' => data_get($this, 'Total'),
            'Type' => data_get($this, 'Type')?->getXeroValue(),
            'CreditNoteNumber' => data_get($this, 'CreditNoteNumber'),
            'LineAmountTypes' => data_get($this, 'LineAmountTypes')?->getXeroValue(),
            'Reference' => data_get($this, 'Reference'),
            'SentToContact' => data_get($this, 'SentToContact'),
            'CurrencyCode' => data_get($this, 'CurrencyCode'),
            'CurrencyRate' => data_get($this, 'CurrencyRate'),
            'RemainingCredit' => data_get($this, 'RemainingCredit'),
            'BrandingThemeID' => data_get($this, 'BrandingThemeID'),
            'HasAttachments' => data_get($this, 'HasAttachments'),
            'FullyPaidOnDate' => data_get($this, 'FullyPaidOnDate'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }
}
