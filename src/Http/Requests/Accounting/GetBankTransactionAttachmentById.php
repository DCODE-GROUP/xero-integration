<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBankTransactionAttachmentById
 */
class GetBankTransactionAttachmentById extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/BankTransactions/{$this->bankTransactionId}/Attachments/{$this->attachmentId}";
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function __construct(
        protected string $bankTransactionId,
        protected string $attachmentId,
        protected string $contentType,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['contentType' => $this->contentType]);
    }
}
