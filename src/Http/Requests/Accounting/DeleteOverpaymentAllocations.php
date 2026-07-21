<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteOverpaymentAllocations
 */
class DeleteOverpaymentAllocations extends Request
{
	protected Method $method = Method::DELETE;


	public function resolveEndpoint(): string
	{
		return "/Overpayments/{$this->overpaymentId}/Allocations/{$this->allocationId}";
	}


	/**
	 * @param string $overpaymentId Unique identifier for a Overpayment
	 * @param string $allocationId Unique identifier for Allocation object
	 */
	public function __construct(
		protected string $overpaymentId,
		protected string $allocationId,
	) {
	}
}
