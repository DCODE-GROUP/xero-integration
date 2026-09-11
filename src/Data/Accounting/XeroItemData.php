<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Item as XeroItem;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroItemData extends AbstractXeroData
{
    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::ITEM;
    }

    protected string $key = 'ItemID';

    protected array $searchFields = [
        'Code',
        'Name',
    ];

    protected array $relatedFields = [
        'PurchaseDetails',
        'SalesDetails',
    ];

    public function __construct(
        public string $Code,
        public string|Optional|null $ItemID = null,
        public string|Optional|null $InventoryAssetAccountCode = null,
        public string|Optional|null $Name = null,
        public bool|Optional|null $IsSold = null,
        public bool|Optional|null $IsPurchased = null,
        public string|Optional|null $Description = null,
        public string|Optional|null $PurchaseDescription = null,
        public array|Optional|null $PurchaseDetails = null,
        public array|Optional|null $SalesDetails = null,
        public bool|Optional|null $IsTrackedAsInventory = null,
        public float|Optional|null $TotalCostPool = null,
        public float|Optional|null $QuantityOnHand = null,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon|Optional|null $UpdatedDateUTC = null,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'ItemID' => data_get($this, 'ItemID'),
            'Code' => data_get($this, 'Code'),
            'InventoryAssetAccountCode' => data_get($this, 'InventoryAssetAccountCode'),
            'Name' => data_get($this, 'Name'),
            'IsSold' => data_get($this, 'IsSold'),
            'IsPurchased' => data_get($this, 'IsPurchased'),
            'Description' => data_get($this, 'Description'),
            'PurchaseDescription' => data_get($this, 'PurchaseDescription'),
            'PurchaseDetails' => data_get($this, 'PurchaseDetails'),
            'SalesDetails' => data_get($this, 'SalesDetails'),
            'IsTrackedAsInventory' => data_get($this, 'IsTrackedAsInventory'),
            'TotalCostPool' => data_get($this, 'TotalCostPool'),
            'QuantityOnHand' => data_get($this, 'QuantityOnHand'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }

    /**
     * Create from Xero Model
     */
    public static function fromXero(XeroModel|XeroItem $xeroItem): self
    {
        $updatedDate = data_get($xeroItem, 'UpdatedDateUTC');

        return new static(
            Code: data_get($xeroItem, 'Code'),
            ItemID: data_get($xeroItem, 'ItemID'),
            InventoryAssetAccountCode: data_get($xeroItem, 'InventoryAssetAccountCode'),
            Name: data_get($xeroItem, 'Name'),
            IsSold: data_get($xeroItem, 'IsSold'),
            IsPurchased: data_get($xeroItem, 'IsPurchased'),
            Description: data_get($xeroItem, 'Description'),
            PurchaseDescription: data_get($xeroItem, 'PurchaseDescription'),
            PurchaseDetails: data_get($xeroItem, 'PurchaseDetails'),
            SalesDetails: data_get($xeroItem, 'SalesDetails'),
            IsTrackedAsInventory: data_get($xeroItem, 'IsTrackedAsInventory'),
            TotalCostPool: data_get($xeroItem, 'TotalCostPool'),
            QuantityOnHand: data_get($xeroItem, 'QuantityOnHand'),
            UpdatedDateUTC: $updatedDate === null ? null : Carbon::instance($updatedDate),
        );
    }
}
