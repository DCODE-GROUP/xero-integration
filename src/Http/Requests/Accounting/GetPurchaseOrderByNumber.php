<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getPurchaseOrderByNumber
 */
class GetPurchaseOrderByNumber extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/PurchaseOrders/{$this->purchaseOrderNumber}";
	}


	/**
	 * @param string $purchaseOrderNumber Unique identifier for a PurchaseOrder
	 */
	public function __construct(
		protected string $purchaseOrderNumber,
	) {
	}
}
