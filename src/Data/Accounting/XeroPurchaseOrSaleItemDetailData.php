<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Data\Normalizers\XeroModelNormalizer;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Override;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroPurchaseOrSaleItemDetailData extends AbstractXeroData
{
    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::ITEM_PURCHASE_OR_SALE;
    }

    protected array $relatedFields = [];

    public function __construct(
        public float $UnitPrice,
        public string $AccountCode,
        public string $TaxType,
        public ?string $COGSAccountCode = null,
    ) {}

    #[Override]
    public static function normalizers(): array
    {
        return array_merge(parent::normalizers(), [XeroModelNormalizer::class]);
    }

    public function toXeroArray(): array
    {
        return [
            'UnitPrice' => data_get($this, 'UnitPrice'),
            'AccountCode' => data_get($this, 'AccountCode'),
            'TaxType' => data_get($this, 'TaxType'),
            'COGSAccountCode' => data_get($this, 'COGSAccountCode'),
        ];
    }

    public static function fromXero(XeroModel $xeroObject): self
    {
        return new static(
            UnitPrice: data_get($xeroObject, 'UnitPrice'),
            AccountCode: data_get($xeroObject, 'AccountCode'),
            TaxType: data_get($xeroObject, 'TaxType'),
            COGSAccountCode: data_get($xeroObject, 'COGSAccountCode'),
        );
    }
}
