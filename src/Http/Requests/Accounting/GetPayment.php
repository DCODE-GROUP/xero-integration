<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getPayment
 */
class GetPayment extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Payments/{$this->paymentId}";
    }

    /**
     * @param  string  $paymentId  Unique identifier for a Payment
     */
    public function __construct(
        protected string $paymentId,
    ) {}
}
