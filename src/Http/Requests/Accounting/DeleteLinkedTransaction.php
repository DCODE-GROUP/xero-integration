<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteLinkedTransaction
 */
class DeleteLinkedTransaction extends Request
{
	protected Method $method = Method::DELETE;


	public function resolveEndpoint(): string
	{
		return "/LinkedTransactions/{$this->linkedTransactionId}";
	}


	/**
	 * @param string $linkedTransactionId Unique identifier for a LinkedTransaction
	 */
	public function __construct(
		protected string $linkedTransactionId,
	) {
	}
}
