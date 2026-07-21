<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getCreditNoteAttachments
 */
class GetCreditNoteAttachments extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/CreditNotes/{$this->creditNoteId}/Attachments";
	}


	/**
	 * @param string $creditNoteId Unique identifier for a Credit Note
	 */
	public function __construct(
		protected string $creditNoteId,
	) {
	}
}
