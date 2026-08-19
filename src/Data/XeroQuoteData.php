<?php

namespace Dcodegroup\XeroIntegration\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\Contracts\XeroSyncable;
use Dcodegroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Dcodegroup\XeroIntegration\Enums\XeroLineAmountTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroQuoteStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Quote as XeroQuote;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroQuoteData extends AbstractXeroData implements XeroSyncable
{
    use XeroSyncTrait;

    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::QUOTE;

    protected string $key = 'QuoteID';

    protected array $searchFields = [
        'QuoteNumber',
    ];

    protected array $relatedFields = [
        'Contact',
        'LineItems',
    ];

    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $ExpiryDate,
        /** @var Collection<int,XeroItemData> $LineItems */
        public Collection $LineItems,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon $UpdatedDateUTC,
        public string $QuoteNumber,
        /** @var XeroContactData|Optional|null $Contact */
        public XeroContactData|Optional|null $Contact = null,
        public XeroQuoteStatusEnum|Optional|null $Status = null,
        public float|Optional|null $SubTotal = null,
        public float|Optional|null $TotalTax = null,
        public float|Optional|null $Total = null,
        public float|Optional|null $TotalDiscount = null,
        public string|Optional|null $QuoteID = null,
        public XeroLineAmountTypeEnum|Optional|null $LineAmountTypes = null,
        public string|Optional|null $CurrencyCode = null,
        public float|Optional|null $CurrencyRate = null,
        public string|Optional|null $Reference = null,
        public string|Optional|null $BrandingThemeID = null,
        public string|Optional|null $Title = null,
        public string|Optional|null $Summary = null,
        public string|Optional|null $Terms = null,
        public string|Optional|null $Url = null,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'Contact' => data_get($this, 'Contact')?->toXeroArray(),
            'Status' => data_get($this, 'Status')?->getXeroValue(),
            'Date' => data_get($this, 'Date'),
            'ExpiryDate' => data_get($this, 'ExpiryDate'),
            'LineItems' => XeroItemData::toXeroCollection(data_get($this, 'LineItems')),
            'LineAmountTypes' => data_get($this, 'LineAmountTypes')?->getXeroValue(),
            'SubTotal' => data_get($this, 'SubTotal'),
            'TotalTax' => data_get($this, 'TotalTax'),
            'Total' => data_get($this, 'Total'),
            'TotalDiscount' => data_get($this, 'TotalDiscount'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
            'CurrencyCode' => data_get($this, 'CurrencyCode'),
            'CurrencyRate' => data_get($this, 'CurrencyRate'),
            'QuoteID' => data_get($this, 'QuoteID'),
            'QuoteNumber' => data_get($this, 'QuoteNumber'),
            'Reference' => data_get($this, 'Reference'),
            'BrandingThemeID' => data_get($this, 'BrandingThemeID'),
            'Title' => data_get($this, 'Title'),
            'Summary' => data_get($this, 'Summary'),
            'Terms' => data_get($this, 'Terms'),
            'Url' => data_get($this, 'Url'),
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  XeroQuote  $xeroObject
     */
    public static function fromXero(XeroModel|XeroQuote $xeroObject): self
    {
        return new static(
            Contact: XeroContactData::fromXero(data_get($xeroObject, 'Contact')),
            Status: XeroQuoteStatusEnum::TryFrom(data_get($xeroObject, 'Status')),
            Date: Carbon::instance(data_get($xeroObject, 'Date')),
            ExpiryDate: Carbon::instance(data_get($xeroObject, 'ExpiryDate')),
            LineItems: collect(data_get($xeroObject, 'LineItems'))->map(fn ($item) => XeroItemData::fromXero($item)),
            SubTotal: data_get($xeroObject, 'SubTotal'),
            TotalTax: data_get($xeroObject, 'TotalTax'),
            Total: data_get($xeroObject, 'Total'),
            TotalDiscount: data_get($xeroObject, 'TotalDiscount'),
            UpdatedDateUTC: Carbon::instance(data_get($xeroObject, 'UpdatedDateUTC')),
            QuoteID: data_get($xeroObject, 'QuoteID'),
            QuoteNumber: data_get($xeroObject, 'QuoteNumber'),
            LineAmountTypes: XeroLineAmountTypeEnum::TryFrom(data_get($xeroObject, 'LineAmountTypes')),
            CurrencyCode: data_get($xeroObject, 'CurrencyCode'),
            CurrencyRate: data_get($xeroObject, 'CurrencyRate'),
            Reference: data_get($xeroObject, 'Reference'),
            BrandingThemeID: data_get($xeroObject, 'BrandingThemeID'),
            Title: data_get($xeroObject, 'Title'),
            Summary: data_get($xeroObject, 'Summary'),
            Terms: data_get($xeroObject, 'Terms'),
            Url: data_get($xeroObject, 'Url'),
        );
    }
}
