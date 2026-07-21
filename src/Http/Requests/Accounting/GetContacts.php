<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getContacts
 */
class GetContacts extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Contacts";
	}


	/**
	 * @param null|string $where Filter by an any element
	 * @param null|string $order Order by an any element
	 * @param null|array $ids Filter by a comma separated list of ContactIDs. Allows you to retrieve a specific set of contacts in a single call.
	 * @param null|int $page e.g. page=1 - Up to 100 contacts will be returned in a single API call.
	 * @param null|bool $includeArchived e.g. includeArchived=true - Contacts with a status of ARCHIVED will be included in the response
	 * @param null|bool $summaryOnly Use summaryOnly=true in GET Contacts and Invoices endpoint to retrieve a smaller version of the response object. This returns only lightweight fields, excluding computation-heavy fields from the response, making the API calls quick and efficient.
	 * @param null|string $searchTerm Search parameter that performs a case-insensitive text search across the Name, FirstName, LastName, ContactNumber and EmailAddress fields.
	 * @param null|int $pageSize Number of records to retrieve per page
	 * @param null|string $ifModifiedSince Only records created or modified since this timestamp will be returned
	 */
	public function __construct(
		protected ?string $where = null,
		protected ?string $order = null,
		protected ?array $ids = null,
		protected ?int $page = null,
		protected ?bool $includeArchived = null,
		protected ?bool $summaryOnly = null,
		protected ?string $searchTerm = null,
		protected ?int $pageSize = null,
		protected ?string $ifModifiedSince = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter([
			'where' => $this->where,
			'order' => $this->order,
			'IDs' => $this->ids,
			'page' => $this->page,
			'includeArchived' => $this->includeArchived,
			'summaryOnly' => $this->summaryOnly,
			'searchTerm' => $this->searchTerm,
			'pageSize' => $this->pageSize,
		]);
	}


	public function defaultHeaders(): array
	{
		return array_filter(['If-Modified-Since' => $this->ifModifiedSince]);
	}
}
