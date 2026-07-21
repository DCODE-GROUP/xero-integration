<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * createPurchaseOrderAttachmentByFileName
 */
class CreatePurchaseOrderAttachmentByFileName extends Request
{
	protected Method $method = Method::PUT;


	public function resolveEndpoint(): string
	{
		return "/PurchaseOrders/{$this->purchaseOrderId}/Attachments/{$this->fileName}";
	}


	/**
	 * @param string $purchaseOrderId Unique identifier for an Purchase Order
	 * @param string $fileName Name of the attachment
	 * @param null|string $idempotencyKey This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
	 */
	public function __construct(
		protected string $purchaseOrderId,
		protected string $fileName,
		protected ?string $idempotencyKey = null,
	) {
	}


	public function defaultHeaders(): array
	{
		return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
	}
}
