<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getRepeatingInvoiceAttachmentById
 */
class GetRepeatingInvoiceAttachmentById extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/RepeatingInvoices/{$this->repeatingInvoiceId}/Attachments/{$this->attachmentId}";
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function __construct(
        protected string $repeatingInvoiceId,
        protected string $attachmentId,
        protected string $contentType,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['contentType' => $this->contentType]);
    }
}
