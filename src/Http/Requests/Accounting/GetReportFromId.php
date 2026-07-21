<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReportFromId
 */
class GetReportFromId extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Reports/{$this->reportId}";
	}


	/**
	 * @param string $reportId Unique identifier for a Report
	 */
	public function __construct(
		protected string $reportId,
	) {
	}
}
