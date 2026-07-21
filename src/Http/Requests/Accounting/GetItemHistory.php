<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getItemHistory
 */
class GetItemHistory extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Items/{$this->itemId}/History";
	}


	/**
	 * @param string $itemId Unique identifier for an Item
	 */
	public function __construct(
		protected string $itemId,
	) {
	}
}
