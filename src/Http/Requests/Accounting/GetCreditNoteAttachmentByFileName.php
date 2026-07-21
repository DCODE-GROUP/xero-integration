<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getCreditNoteAttachmentByFileName
 */
class GetCreditNoteAttachmentByFileName extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/CreditNotes/{$this->creditNoteId}/Attachments/{$this->fileName}";
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function __construct(
        protected string $creditNoteId,
        protected string $fileName,
        protected string $contentType,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['contentType' => $this->contentType]);
    }
}
