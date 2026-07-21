<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getTaxRateByTaxType
 */
class GetTaxRateByTaxType extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/TaxRates/{$this->taxType}";
    }

    /**
     * @param  string  $taxType  A valid TaxType code
     */
    public function __construct(
        protected string $taxType,
    ) {}
}
