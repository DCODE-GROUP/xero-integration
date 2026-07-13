<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use DcodeGroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Quote as XeroQuote;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroQuoteData extends AbstractXeroData implements XeroSyncable
{
    use XeroSyncTrait;

    protected string $xeroRelationship = 'quote';

    protected array $searchFields = [
        'QuoteNumber',
    ];

    protected array $relatedFields = [
        'Contact',
        'LineItems',
    ];

    final public function __construct(
        /** @var XeroContactData|Optional|null $Contact */
        public XeroContactData|Optional|null $Contact,
        public string|Optional|null $Status, // ToDo: Make Enum
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $ExpiryDate,
        /** @var Collection<int,XeroQuoteItemData> $LineItems */
        public Collection $LineItems,
        public float|Optional|null $SubTotal,
        public float|Optional|null $TotalTax,
        public float|Optional|null $Total,
        public float|Optional|null $TotalDiscount,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon $UpdatedDateUTC,
        public string|Optional|null $QuoteID,
        public string $QuoteNumber,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'Contact' => data_get($this, 'Contact')?->toXeroArray(),
            'Status' => data_get($this, 'Status'),
            'Date' => data_get($this, 'Date'),
            'ExpiryDate' => data_get($this, 'ExpiryDate'),
            'LineItems' => XeroQuoteItemData::toXeroCollection(data_get($this, 'LineItems')),
            'SubTotal' => data_get($this, 'SubTotal'),
            'TotalTax' => data_get($this, 'TotalTax'),
            'Total' => data_get($this, 'Total'),
            'TotalDiscount' => data_get($this, 'TotalDiscount'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
            'QuoteID' => data_get($this, 'QuoteID'),
            'QuoteNumber' => data_get($this, 'QuoteNumber'),
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  XeroQuote  $xeroObject
     */
    protected static function fromXero(XeroModel|XeroQuote $xeroObject): self
    {
        return new static(
            Contact: XeroContactData::fromXero(data_get($xeroObject, 'Contact')),
            Status: data_get($xeroObject, 'Status'),
            Date: Carbon::instance(data_get($xeroObject, 'Date')),
            ExpiryDate: Carbon::instance(data_get($xeroObject, 'ExpiryDate')),
            LineItems: collect(data_get($xeroObject, 'LineItems'))->map(fn ($item) => XeroQuoteItemData::fromXero($item)),
            SubTotal: data_get($xeroObject, 'SubTotal'),
            TotalTax: data_get($xeroObject, 'TotalTax'),
            Total: data_get($xeroObject, 'Total'),
            TotalDiscount: data_get($xeroObject, 'TotalDiscount'),
            UpdatedDateUTC: Carbon::instance(data_get($xeroObject, 'UpdatedDateUTC')),
            QuoteID: data_get($xeroObject, 'QuoteID'),
            QuoteNumber: data_get($xeroObject, 'QuoteNumber'),
        );
    }
}
