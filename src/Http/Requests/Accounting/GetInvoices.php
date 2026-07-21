<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getInvoices
 */
class GetInvoices extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Invoices";
	}


	/**
	 * @param null|string $where Filter by an any element
	 * @param null|string $order Order by an any element
	 * @param null|array $ids Filter by a comma-separated list of InvoicesIDs.
	 * @param null|array $invoiceNumbers Filter by a comma-separated list of InvoiceNumbers.
	 * @param null|array $contactIds Filter by a comma-separated list of ContactIDs.
	 * @param null|array $statuses Filter by a comma-separated list Statuses. For faster response times we recommend using these explicit parameters instead of passing OR conditions into the Where filter.
	 * @param null|int $page e.g. page=1 – Up to 100 invoices will be returned in a single API call with line items shown for each invoice
	 * @param null|bool $includeArchived e.g. includeArchived=true - Invoices with a status of ARCHIVED will be included in the response
	 * @param null|bool $createdByMyApp When set to true you'll only retrieve Invoices created by your app
	 * @param null|int $unitdp e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
	 * @param null|bool $summaryOnly Use summaryOnly=true in GET Contacts and Invoices endpoint to retrieve a smaller version of the response object. This returns only lightweight fields, excluding computation-heavy fields from the response, making the API calls quick and efficient.
	 * @param null|int $pageSize Number of records to retrieve per page
	 * @param null|string $searchTerm Search parameter that performs a case-insensitive text search across the fields e.g. InvoiceNumber, Reference.
	 * @param null|string $ifModifiedSince Only records created or modified since this timestamp will be returned
	 */
	public function __construct(
		protected ?string $where = null,
		protected ?string $order = null,
		protected ?array $ids = null,
		protected ?array $invoiceNumbers = null,
		protected ?array $contactIds = null,
		protected ?array $statuses = null,
		protected ?int $page = null,
		protected ?bool $includeArchived = null,
		protected ?bool $createdByMyApp = null,
		protected ?int $unitdp = null,
		protected ?bool $summaryOnly = null,
		protected ?int $pageSize = null,
		protected ?string $searchTerm = null,
		protected ?string $ifModifiedSince = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter([
			'where' => $this->where,
			'order' => $this->order,
			'IDs' => $this->ids,
			'InvoiceNumbers' => $this->invoiceNumbers,
			'ContactIDs' => $this->contactIds,
			'Statuses' => $this->statuses,
			'page' => $this->page,
			'includeArchived' => $this->includeArchived,
			'createdByMyApp' => $this->createdByMyApp,
			'unitdp' => $this->unitdp,
			'summaryOnly' => $this->summaryOnly,
			'pageSize' => $this->pageSize,
			'searchTerm' => $this->searchTerm,
		]);
	}


	public function defaultHeaders(): array
	{
		return array_filter(['If-Modified-Since' => $this->ifModifiedSince]);
	}
}
