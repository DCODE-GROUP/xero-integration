<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateCreditNoteAttachmentByFileName
 */
class UpdateCreditNoteAttachmentByFileName extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/CreditNotes/{$this->creditNoteId}/Attachments/{$this->fileName}";
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  string  $fileName  Name of the attachment
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $creditNoteId,
        protected string $fileName,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
