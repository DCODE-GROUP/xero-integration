<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getPayments
 */
class GetPayments extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Payments";
	}


	/**
	 * @param null|string $where Filter by an any element
	 * @param null|string $order Order by an any element
	 * @param null|int $page Up to 100 payments will be returned in a single API call
	 * @param null|int $pageSize Number of records to retrieve per page
	 * @param null|string $ifModifiedSince Only records created or modified since this timestamp will be returned
	 */
	public function __construct(
		protected ?string $where = null,
		protected ?string $order = null,
		protected ?int $page = null,
		protected ?int $pageSize = null,
		protected ?string $ifModifiedSince = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['where' => $this->where, 'order' => $this->order, 'page' => $this->page, 'pageSize' => $this->pageSize]);
	}


	public function defaultHeaders(): array
	{
		return array_filter(['If-Modified-Since' => $this->ifModifiedSince]);
	}
}
