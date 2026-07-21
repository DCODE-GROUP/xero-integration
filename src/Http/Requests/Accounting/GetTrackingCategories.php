<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getTrackingCategories
 */
class GetTrackingCategories extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/TrackingCategories";
	}


	/**
	 * @param null|string $where Filter by an any element
	 * @param null|string $order Order by an any element
	 * @param null|bool $includeArchived e.g. includeArchived=true - Categories and options with a status of ARCHIVED will be included in the response
	 */
	public function __construct(
		protected ?string $where = null,
		protected ?string $order = null,
		protected ?bool $includeArchived = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['where' => $this->where, 'order' => $this->order, 'includeArchived' => $this->includeArchived]);
	}
}
