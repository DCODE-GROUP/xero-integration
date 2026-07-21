<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * createInvoiceAttachmentByFileName
 */
class CreateInvoiceAttachmentByFileName extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return "/Invoices/{$this->invoiceId}/Attachments/{$this->fileName}";
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     * @param  string  $fileName  Name of the attachment
     * @param  null|bool  $includeOnline  Allows an attachment to be seen by the end customer within their online invoice
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $invoiceId,
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
