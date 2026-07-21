<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * deleteBatchPaymentByUrlParam
 */
class DeleteBatchPaymentByUrlParam extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/BatchPayments/{$this->batchPaymentId}";
    }

    /**
     * @param  string  $batchPaymentId  Unique identifier for BatchPayment
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $batchPaymentId,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
