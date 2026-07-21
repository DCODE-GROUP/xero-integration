<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * createRepeatingInvoiceHistory
 */
class CreateRepeatingInvoiceHistory extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return "/RepeatingInvoices/{$this->repeatingInvoiceId}/History";
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $repeatingInvoiceId,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
