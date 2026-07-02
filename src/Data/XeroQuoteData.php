<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Quote as XeroQuote;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroQuoteData extends Data implements XeroSyncable
{
    final public function __construct(
        public XeroContactData|Optional|null $Contact,
        public string|Optional|null $Status,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $ExpiryDate,
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
        // Ensure we don't overwrite the Contact in Xero if we have a local model with a Xero ID
        // otherwise we may overwrite the Contact details in Xero with outdated information from our local model
        $contactData = $this->Contact->localModel() ? ['ContactID' => $this->Contact->ContactID] : $this->Contact->toXeroArray();

        return  [
            'Contact' => $contactData,
            'Status' => $this->Status,
            'Date' => $this->Date,
            'ExpiryDate' => $this->ExpiryDate,
            'LineItems' => $this->LineItems->map(function (XeroQuoteItemData $item) {
                return $item->toXeroArray();
            })->toArray(),
            'SubTotal' => $this->SubTotal,
            'TotalTax' => $this->TotalTax,
            'Total' => $this->Total,
            'TotalDiscount' => $this->TotalDiscount,
            'UpdatedDateUTC' => $this->UpdatedDateUTC,
            'QuoteID' => $this->QuoteID,
            'QuoteNumber' => $this->QuoteNumber,
        ];
    }

    /**
     * Create from Xero Model
     * @param XeroQuote $xeroObject
     * @return self
     */
    public static function fromXero(XeroModel|XeroQuote $xeroObject): self
    {
        return new static(
            Contact: XeroContactData::fromXero($xeroObject->getContact()),
            Status: $xeroObject->getStatus(),
            Date: Carbon::instance($xeroObject->getDate()),
            ExpiryDate: Carbon::instance($xeroObject->getExpiryDate()),
            LineItems: collect($xeroObject->getLineItems())->map(fn ($item) => XeroQuoteItemData::fromXero($item)),
            SubTotal: $xeroObject->getSubTotal(),
            TotalTax: $xeroObject->getTotalTax(),
            Total: $xeroObject->getTotal(),
            TotalDiscount: $xeroObject->getTotalDiscount(),
            UpdatedDateUTC: Carbon::instance($xeroObject->getUpdatedDateUTC()),
            QuoteID: $xeroObject->getQuoteID(),
            QuoteNumber: $xeroObject->getQuoteNumber(),
        );
    }

    abstract public static function fromModel(Model $model): self;

    abstract public function syncToModel(): Model;

    abstract public function localModel(): ?Model;
}
