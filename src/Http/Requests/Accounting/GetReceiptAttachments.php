<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReceiptAttachments
 */
class GetReceiptAttachments extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Receipts/{$this->receiptId}/Attachments";
	}


	/**
	 * @param string $receiptId Unique identifier for a Receipt
	 */
	public function __construct(
		protected string $receiptId,
	) {
	}
}
