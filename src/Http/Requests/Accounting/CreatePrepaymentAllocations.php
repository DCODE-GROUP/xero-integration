<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * createPrepaymentAllocations
 */
class CreatePrepaymentAllocations extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return "/Prepayments/{$this->prepaymentId}/Allocations";
    }

    /**
     * @param  string  $prepaymentId  Unique identifier for a PrePayment
     * @param  null|bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $prepaymentId,
        protected ?bool $summarizeErrors = null,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['summarizeErrors' => $this->summarizeErrors]);
    }

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
