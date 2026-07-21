<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getPaymentHistory
 */
class GetPaymentHistory extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Payments/{$this->paymentId}/History";
	}


	/**
	 * @param string $paymentId Unique identifier for a Payment
	 */
	public function __construct(
		protected string $paymentId,
	) {
	}
}
