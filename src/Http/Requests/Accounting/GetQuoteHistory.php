<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getQuoteHistory
 */
class GetQuoteHistory extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Quotes/{$this->quoteId}/History";
	}


	/**
	 * @param string $quoteId Unique identifier for an Quote
	 */
	public function __construct(
		protected string $quoteId,
	) {
	}
}
