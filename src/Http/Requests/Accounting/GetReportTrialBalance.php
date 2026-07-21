<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReportTrialBalance
 */
class GetReportTrialBalance extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Reports/TrialBalance';
    }

    /**
     * @param  null|string  $date  The date for the Trial Balance report e.g. 2018-03-31
     * @param  null|bool  $paymentsOnly  Return cash only basis for the Trial Balance report
     */
    public function __construct(
        protected ?string $date = null,
        protected ?bool $paymentsOnly = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['date' => $this->date, 'paymentsOnly' => $this->paymentsOnly]);
    }
}
