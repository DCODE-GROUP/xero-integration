<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getPrepayment
 */
class GetPrepayment extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Prepayments/{$this->prepaymentId}";
    }

    /**
     * @param  string  $prepaymentId  Unique identifier for a PrePayment
     */
    public function __construct(
        protected string $prepaymentId,
    ) {}
}
