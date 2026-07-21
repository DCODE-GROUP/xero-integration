<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteCreditNoteAllocations
 */
class DeleteCreditNoteAllocations extends Request
{
	protected Method $method = Method::DELETE;


	public function resolveEndpoint(): string
	{
		return "/CreditNotes/{$this->creditNoteId}/Allocations/{$this->allocationId}";
	}


	/**
	 * @param string $creditNoteId Unique identifier for a Credit Note
	 * @param string $allocationId Unique identifier for Allocation object
	 */
	public function __construct(
		protected string $creditNoteId,
		protected string $allocationId,
	) {
	}
}
