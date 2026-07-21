<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getPurchaseOrderAsPdf
 */
class GetPurchaseOrderAsPdf extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/PurchaseOrders/{$this->purchaseOrderId}/pdf";
	}


	/**
	 * @param string $purchaseOrderId Unique identifier for an Purchase Order
	 */
	public function __construct(
		protected string $purchaseOrderId,
	) {
	}
}
