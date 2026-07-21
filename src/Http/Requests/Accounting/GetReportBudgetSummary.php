<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReportBudgetSummary
 */
class GetReportBudgetSummary extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Reports/BudgetSummary";
	}


	/**
	 * @param null|string $date The date for the Bank Summary report e.g. 2018-03-31
	 * @param null|int $periods The number of periods to compare (integer between 1 and 12)
	 * @param null|int $timeframe The period size to compare to (1=month, 3=quarter, 12=year)
	 */
	public function __construct(
		protected ?string $date = null,
		protected ?int $periods = null,
		protected ?int $timeframe = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['date' => $this->date, 'periods' => $this->periods, 'timeframe' => $this->timeframe]);
	}
}
