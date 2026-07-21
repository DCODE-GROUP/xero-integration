<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBudget
 */
class GetBudget extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Budgets/{$this->budgetId}";
	}


	/**
	 * @param string $budgetId Unique identifier for Budgets
	 * @param null|string $dateTo Filter by start date
	 * @param null|string $dateFrom Filter by end date
	 */
	public function __construct(
		protected string $budgetId,
		protected ?string $dateTo = null,
		protected ?string $dateFrom = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['DateTo' => $this->dateTo, 'DateFrom' => $this->dateFrom]);
	}
}
