<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReportBalanceSheet
 */
class GetReportBalanceSheet extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Reports/BalanceSheet";
	}


	/**
	 * @param null|string $date The date of the Balance Sheet report
	 * @param null|int $periods The number of periods for the Balance Sheet report
	 * @param null|string $timeframe The period size to compare to (MONTH, QUARTER, YEAR)
	 * @param null|string $trackingOptionId1 The tracking option 1 for the Balance Sheet report
	 * @param null|string $trackingOptionId2 The tracking option 2 for the Balance Sheet report
	 * @param null|bool $standardLayout The standard layout boolean for the Balance Sheet report
	 * @param null|bool $paymentsOnly return a cash basis for the Balance Sheet report
	 */
	public function __construct(
		protected ?string $date = null,
		protected ?int $periods = null,
		protected ?string $timeframe = null,
		protected ?string $trackingOptionId1 = null,
		protected ?string $trackingOptionId2 = null,
		protected ?bool $standardLayout = null,
		protected ?bool $paymentsOnly = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter([
			'date' => $this->date,
			'periods' => $this->periods,
			'timeframe' => $this->timeframe,
			'trackingOptionID1' => $this->trackingOptionId1,
			'trackingOptionID2' => $this->trackingOptionId2,
			'standardLayout' => $this->standardLayout,
			'paymentsOnly' => $this->paymentsOnly,
		]);
	}
}
