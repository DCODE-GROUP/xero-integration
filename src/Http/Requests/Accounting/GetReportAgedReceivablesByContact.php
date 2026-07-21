<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReportAgedReceivablesByContact
 */
class GetReportAgedReceivablesByContact extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Reports/AgedReceivablesByContact";
	}


	/**
	 * @param string $contactId Unique identifier for a Contact
	 * @param null|string $date The date of the Aged Receivables By Contact report
	 * @param null|string $fromDate filter by the from date of the report e.g. 2021-02-01
	 * @param null|string $toDate filter by the to date of the report e.g. 2021-02-28
	 */
	public function __construct(
		protected string $contactId,
		protected ?string $date = null,
		protected ?string $fromDate = null,
		protected ?string $toDate = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['contactId' => $this->contactId, 'date' => $this->date, 'fromDate' => $this->fromDate, 'toDate' => $this->toDate]);
	}
}
