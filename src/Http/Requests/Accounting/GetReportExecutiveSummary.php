<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReportExecutiveSummary
 */
class GetReportExecutiveSummary extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Reports/ExecutiveSummary';
    }

    /**
     * @param  null|string  $date  The date for the Bank Summary report e.g. 2018-03-31
     */
    public function __construct(
        protected ?string $date = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['date' => $this->date]);
    }
}
