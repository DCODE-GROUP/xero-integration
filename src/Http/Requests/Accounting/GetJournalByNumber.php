<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getJournalByNumber
 */
class GetJournalByNumber extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Journals/{$this->journalNumber}";
	}


	/**
	 * @param int $journalNumber Number of a Journal
	 */
	public function __construct(
		protected int $journalNumber,
	) {
	}
}
