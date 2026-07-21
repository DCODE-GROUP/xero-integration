<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getOnlineInvoice
 */
class GetOnlineInvoice extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Invoices/{$this->invoiceId}/OnlineInvoice";
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     */
    public function __construct(
        protected string $invoiceId,
    ) {}
}
