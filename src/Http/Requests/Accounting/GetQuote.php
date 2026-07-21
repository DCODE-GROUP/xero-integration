<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getQuote
 */
class GetQuote extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Quotes/{$this->quoteId}";
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     */
    public function __construct(
        protected string $quoteId,
    ) {}
}
