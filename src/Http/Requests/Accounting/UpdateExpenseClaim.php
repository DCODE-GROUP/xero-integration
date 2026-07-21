<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateExpenseClaim
 */
class UpdateExpenseClaim extends Request implements HasBody
{
	use HasJsonBody;

	protected Method $method = Method::POST;


	public function resolveEndpoint(): string
	{
		return "/ExpenseClaims/{$this->expenseClaimId}";
	}


	/**
	 * @param string $expenseClaimId Unique identifier for a ExpenseClaim
	 * @param null|string $idempotencyKey This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
	 */
	public function __construct(
		protected string $expenseClaimId,
		protected ?string $idempotencyKey = null,
	) {
	}


	public function defaultHeaders(): array
	{
		return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
	}
}
