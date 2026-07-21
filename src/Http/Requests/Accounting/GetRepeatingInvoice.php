<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getRepeatingInvoice
 */
class GetRepeatingInvoice extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/RepeatingInvoices/{$this->repeatingInvoiceId}";
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     */
    public function __construct(
        protected string $repeatingInvoiceId,
    ) {}
}
