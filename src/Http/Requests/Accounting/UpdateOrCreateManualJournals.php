<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateOrCreateManualJournals
 */
class UpdateOrCreateManualJournals extends Request implements HasBody
{
	use HasJsonBody;

	protected Method $method = Method::POST;


	public function resolveEndpoint(): string
	{
		return "/ManualJournals";
	}


	/**
	 * @param null|bool $summarizeErrors If false return 200 OK and mix of successfully created objects and any with validation errors
	 * @param null|string $idempotencyKey This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
	 */
	public function __construct(
		protected ?bool $summarizeErrors = null,
		protected ?string $idempotencyKey = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['summarizeErrors' => $this->summarizeErrors]);
	}


	public function defaultHeaders(): array
	{
		return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
	}
}
