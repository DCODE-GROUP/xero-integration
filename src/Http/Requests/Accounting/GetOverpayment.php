<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getOverpayment
 */
class GetOverpayment extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Overpayments/{$this->overpaymentId}";
	}


	/**
	 * @param string $overpaymentId Unique identifier for a Overpayment
	 */
	public function __construct(
		protected string $overpaymentId,
	) {
	}
}
