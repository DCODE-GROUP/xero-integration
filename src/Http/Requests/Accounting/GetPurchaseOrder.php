<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getPurchaseOrder
 */
class GetPurchaseOrder extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/PurchaseOrders/{$this->purchaseOrderId}";
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     */
    public function __construct(
        protected string $purchaseOrderId,
    ) {}
}
