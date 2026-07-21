<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getContactGroups
 */
class GetContactGroups extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/ContactGroups";
	}


	/**
	 * @param null|string $where Filter by an any element
	 * @param null|string $order Order by an any element
	 */
	public function __construct(
		protected ?string $where = null,
		protected ?string $order = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['where' => $this->where, 'order' => $this->order]);
	}
}
