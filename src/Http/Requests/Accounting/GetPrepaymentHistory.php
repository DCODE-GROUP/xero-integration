<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getPrepaymentHistory
 */
class GetPrepaymentHistory extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Prepayments/{$this->prepaymentId}/History";
    }

    /**
     * @param  string  $prepaymentId  Unique identifier for a PrePayment
     */
    public function __construct(
        protected string $prepaymentId,
    ) {}
}
