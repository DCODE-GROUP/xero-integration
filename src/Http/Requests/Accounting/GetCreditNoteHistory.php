<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getCreditNoteHistory
 */
class GetCreditNoteHistory extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/CreditNotes/{$this->creditNoteId}/History";
	}


	/**
	 * @param string $creditNoteId Unique identifier for a Credit Note
	 */
	public function __construct(
		protected string $creditNoteId,
	) {
	}
}
