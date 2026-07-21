<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getPurchaseOrders
 */
class GetPurchaseOrders extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/PurchaseOrders";
	}


	/**
	 * @param null|string $status Filter by purchase order status
	 * @param null|string $dateFrom Filter by purchase order date (e.g. GET https://.../PurchaseOrders?DateFrom=2015-12-01&DateTo=2015-12-31
	 * @param null|string $dateTo Filter by purchase order date (e.g. GET https://.../PurchaseOrders?DateFrom=2015-12-01&DateTo=2015-12-31
	 * @param null|string $order Order by an any element
	 * @param null|int $page To specify a page, append the page parameter to the URL e.g. ?page=1. If there are 100 records in the response you will need to check if there is any more data by fetching the next page e.g ?page=2 and continuing this process until no more results are returned.
	 * @param null|int $pageSize Number of records to retrieve per page
	 * @param null|string $ifModifiedSince Only records created or modified since this timestamp will be returned
	 */
	public function __construct(
		protected ?string $status = null,
		protected ?string $dateFrom = null,
		protected ?string $dateTo = null,
		protected ?string $order = null,
		protected ?int $page = null,
		protected ?int $pageSize = null,
		protected ?string $ifModifiedSince = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter([
			'Status' => $this->status,
			'DateFrom' => $this->dateFrom,
			'DateTo' => $this->dateTo,
			'order' => $this->order,
			'page' => $this->page,
			'pageSize' => $this->pageSize,
		]);
	}


	public function defaultHeaders(): array
	{
		return array_filter(['If-Modified-Since' => $this->ifModifiedSince]);
	}
}
