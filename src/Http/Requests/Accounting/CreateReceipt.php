<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * createReceipt
 */
class CreateReceipt extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return '/Receipts';
    }

    /**
     * @param  null|int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected ?int $unitdp = null,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['unitdp' => $this->unitdp]);
    }

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
