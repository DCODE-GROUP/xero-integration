<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getRepeatingInvoiceAttachments
 */
class GetRepeatingInvoiceAttachments extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/RepeatingInvoices/{$this->repeatingInvoiceId}/Attachments";
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     */
    public function __construct(
        protected string $repeatingInvoiceId,
    ) {}
}
