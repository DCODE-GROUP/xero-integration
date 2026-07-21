<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBatchPayment
 */
class GetBatchPayment extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/BatchPayments/{$this->batchPaymentId}";
	}


	/**
	 * @param string $batchPaymentId Unique identifier for BatchPayment
	 */
	public function __construct(
		protected string $batchPaymentId,
	) {
	}
}
