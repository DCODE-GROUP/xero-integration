<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getItem
 */
class GetItem extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Items/{$this->itemId}";
	}


	/**
	 * @param string $itemId Unique identifier for an Item
	 * @param null|int $unitdp e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
	 */
	public function __construct(
		protected string $itemId,
		protected ?int $unitdp = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['unitdp' => $this->unitdp]);
	}
}
