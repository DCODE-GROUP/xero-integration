<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getOverpaymentHistory
 */
class GetOverpaymentHistory extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Overpayments/{$this->overpaymentId}/History";
    }

    /**
     * @param  string  $overpaymentId  Unique identifier for a Overpayment
     */
    public function __construct(
        protected string $overpaymentId,
    ) {}
}
