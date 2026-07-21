<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * createCreditNoteAttachmentByFileName
 */
class CreateCreditNoteAttachmentByFileName extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return "/CreditNotes/{$this->creditNoteId}/Attachments/{$this->fileName}";
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  string  $fileName  Name of the attachment
     * @param  null|bool  $includeOnline  Allows an attachment to be seen by the end customer within their online invoice
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $creditNoteId,
        protected string $fileName,
        protected ?bool $includeOnline = null,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['IncludeOnline' => $this->includeOnline]);
    }

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
