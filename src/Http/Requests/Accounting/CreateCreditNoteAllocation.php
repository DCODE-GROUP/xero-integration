<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * createCreditNoteAllocation
 */
class CreateCreditNoteAllocation extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return "/CreditNotes/{$this->creditNoteId}/Allocations";
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  null|bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $creditNoteId,
        protected ?bool $summarizeErrors = null,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['summarizeErrors' => $this->summarizeErrors]);
    }

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
