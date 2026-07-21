<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBudgets
 */
class GetBudgets extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Budgets";
	}


	/**
	 * @param null|string $ids Filter by BudgetID. Allows you to retrieve a specific individual budget.
	 * @param null|string $dateTo Filter by start date
	 * @param null|string $dateFrom Filter by end date
	 */
	public function __construct(
		protected ?string $ids = null,
		protected ?string $dateTo = null,
		protected ?string $dateFrom = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['IDs' => $this->ids, 'DateTo' => $this->dateTo, 'DateFrom' => $this->dateFrom]);
	}
}
