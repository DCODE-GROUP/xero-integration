<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBankTransfers
 */
class GetBankTransfers extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/BankTransfers";
	}


	/**
	 * @param null|string $where Filter by an any element
	 * @param null|string $order Order by an any element
	 * @param null|string $ifModifiedSince Only records created or modified since this timestamp will be returned
	 */
	public function __construct(
		protected ?string $where = null,
		protected ?string $order = null,
		protected ?string $ifModifiedSince = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['where' => $this->where, 'order' => $this->order]);
	}


	public function defaultHeaders(): array
	{
		return array_filter(['If-Modified-Since' => $this->ifModifiedSince]);
	}
}
