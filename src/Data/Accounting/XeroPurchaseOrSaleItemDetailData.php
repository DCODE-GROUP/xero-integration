<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Dcodegroup\XeroIntegration\Data\Normalizers\XeroModelNormalizer;
use Override;
use Spatie\LaravelData\Data;

class XeroPurchaseOrSaleItemDetailData extends Data
{
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
}
