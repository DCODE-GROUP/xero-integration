<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteItem
 */
class DeleteItem extends Request
{
	protected Method $method = Method::DELETE;


	public function resolveEndpoint(): string
	{
		return "/Items/{$this->itemId}";
	}


	/**
	 * @param string $itemId Unique identifier for an Item
	 */
	public function __construct(
		protected string $itemId,
	) {
	}
}
