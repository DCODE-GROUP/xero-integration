<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReportTenNinetyNine
 */
class GetReportTenNinetyNine extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Reports/TenNinetyNine";
	}


	/**
	 * @param null|string $reportYear The year of the 1099 report
	 */
	public function __construct(
		protected ?string $reportYear = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['reportYear' => $this->reportYear]);
	}
}
