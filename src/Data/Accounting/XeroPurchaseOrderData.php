<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Dcodegroup\XeroIntegration\Enums\XeroLineAmountTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroPurchaseOrderStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\PurchaseOrder as XeroPurchaseOrder;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroPurchaseOrderData extends AbstractXeroData
{
    use XeroSyncTrait;

    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::PURCHASE_ORDER;
    }

    protected string $key = 'PurchaseOrderID';

    protected array $searchFields = [
        'PurchaseOrderNumber',
    ];

    protected array $relatedFields = [
        'Contact',
        'LineItems',
    ];

    public function __construct(
        public XeroContactData $Contact,
        /** @var Collection<int,XeroLineItemData> */
        public Collection $LineItems,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        public Carbon|Optional|null $DeliveryDate = null,
        public XeroLineAmountTypeEnum|Optional|null $LineAmountTypes = null,
        public string|Optional|null $PurchaseOrderNumber = null,
        public string|Optional|null $Reference = null,
        public string|Optional|null $BrandingThemeID = null,
        public XeroPurchaseOrderStatusEnum|Optional|null $Status = null,
        public bool|Optional|null $SentToContact = null,
        public string|Optional|null $DeliveryAddress = null,
        public string|Optional|null $AttentionTo = null,
        public string|Optional|null $Telephone = null,
        public string|Optional|null $DeliveryInstructions = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $ExpectedArrivalDate = null,
        public string|Optional|null $PurchaseOrderID = null,
        public string|Optional|null $CurrencyCode = null,
        public float|Optional|null $CurrencyRate = null,
        public float|Optional|null $SubTotal = null,
        public float|Optional|null $TotalTax = null,
        public float|Optional|null $Total = null,
        public float|Optional|null $TotalDiscount = null,
        public bool|Optional|null $HasAttachments = null,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon|Optional|null $UpdatedDateUTC = null,
    ) {}

    public static function fromXero(XeroModel|XeroPurchaseOrder $xeroPurchaseOrder): self
    {
        $updatedDate = data_get($xeroPurchaseOrder, 'UpdatedDateUTC');

        return new static(
            Contact: XeroContactData::fromXero(data_get($xeroPurchaseOrder, 'Contact')),
            LineItems: XeroLineItemData::toCollection(data_get($xeroPurchaseOrder, 'LineItems')) ?? collect(),
            Date: Carbon::instance(data_get($xeroPurchaseOrder, 'Date')),
            DeliveryDate: data_get($xeroPurchaseOrder, 'DeliveryDate') ? Carbon::instance(data_get($xeroPurchaseOrder, 'DeliveryDate')) : null,
            LineAmountTypes: ($lineAmountTypes = data_get($xeroPurchaseOrder, 'LineAmountTypes')) === null
                ? null
                : XeroLineAmountTypeEnum::tryFrom(strtolower($lineAmountTypes)),
            PurchaseOrderNumber: data_get($xeroPurchaseOrder, 'PurchaseOrderNumber'),
            Reference: data_get($xeroPurchaseOrder, 'Reference'),
            BrandingThemeID: data_get($xeroPurchaseOrder, 'BrandingThemeID'),
            Status: XeroPurchaseOrderStatusEnum::fromXero(data_get($xeroPurchaseOrder, 'Status')),
            SentToContact: data_get($xeroPurchaseOrder, 'SentToContact'),
            DeliveryAddress: data_get($xeroPurchaseOrder, 'DeliveryAddress'),
            AttentionTo: data_get($xeroPurchaseOrder, 'AttentionTo'),
            Telephone: data_get($xeroPurchaseOrder, 'Telephone'),
            DeliveryInstructions: data_get($xeroPurchaseOrder, 'DeliveryInstructions'),
            ExpectedArrivalDate: data_get($xeroPurchaseOrder, 'ExpectedArrivalDate') ? Carbon::instance(data_get($xeroPurchaseOrder, 'ExpectedArrivalDate')) : null,
            PurchaseOrderID: data_get($xeroPurchaseOrder, 'PurchaseOrderID'),
            CurrencyCode: data_get($xeroPurchaseOrder, 'CurrencyCode'),
            CurrencyRate: data_get($xeroPurchaseOrder, 'CurrencyRate'),
            SubTotal: data_get($xeroPurchaseOrder, 'SubTotal'),
            TotalTax: data_get($xeroPurchaseOrder, 'TotalTax'),
            Total: data_get($xeroPurchaseOrder, 'Total'),
            TotalDiscount: data_get($xeroPurchaseOrder, 'TotalDiscount'),
            HasAttachments: data_get($xeroPurchaseOrder, 'HasAttachments'),
            UpdatedDateUTC: $updatedDate === null ? null : Carbon::instance($updatedDate),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'Contact' => $this->Contact->toXeroArray(),
            'LineItems' => XeroLineItemData::toXeroCollection($this->LineItems),
            'Date' => $this->Date,
            'DeliveryDate' => data_get($this, 'DeliveryDate'),
            'LineAmountTypes' => data_get($this, 'LineAmountTypes')?->getXeroValue(),
            'PurchaseOrderNumber' => data_get($this, 'PurchaseOrderNumber'),
            'Reference' => data_get($this, 'Reference'),
            'BrandingThemeID' => data_get($this, 'BrandingThemeID'),
            'Status' => data_get($this, 'Status')?->getXeroValue(),
            'SentToContact' => data_get($this, 'SentToContact'),
            'DeliveryAddress' => data_get($this, 'DeliveryAddress'),
            'AttentionTo' => data_get($this, 'AttentionTo'),
            'Telephone' => data_get($this, 'Telephone'),
            'DeliveryInstructions' => data_get($this, 'DeliveryInstructions'),
            'ExpectedArrivalDate' => data_get($this, 'ExpectedArrivalDate'),
            'PurchaseOrderID' => data_get($this, 'PurchaseOrderID'),
            'CurrencyCode' => data_get($this, 'CurrencyCode'),
            'CurrencyRate' => data_get($this, 'CurrencyRate'),
            'SubTotal' => data_get($this, 'SubTotal'),
            'TotalTax' => data_get($this, 'TotalTax'),
            'Total' => data_get($this, 'Total'),
            'TotalDiscount' => data_get($this, 'TotalDiscount'),
            'HasAttachments' => data_get($this, 'HasAttachments'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }
}
