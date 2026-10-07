<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\PurchaseOrder as XeroPurchaseOrder;

enum XeroPurchaseOrderStatusEnum: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case AUTHORISED = 'authorised';
    case BILLED = 'billed';
    case DELETED = 'deleted';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::AUTHORISED => 'Authorised',
            self::BILLED => 'Billed',
            self::DELETED => 'Deleted',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::DRAFT => XeroPurchaseOrder::PURCHASE_ORDER_STATUS_DRAFT,
            self::SUBMITTED => XeroPurchaseOrder::PURCHASE_ORDER_STATUS_SUBMITTED,
            self::AUTHORISED => XeroPurchaseOrder::PURCHASE_ORDER_STATUS_AUTHORISED,
            self::BILLED => XeroPurchaseOrder::PURCHASE_ORDER_STATUS_BILLED,
            self::DELETED => XeroPurchaseOrder::PURCHASE_ORDER_STATUS_DELETED,
        };
    }

    public static function fromXero(?string $xeroValue): ?self
    {
        return $xeroValue === null ? null : self::tryFrom(strtolower($xeroValue));
    }
}
