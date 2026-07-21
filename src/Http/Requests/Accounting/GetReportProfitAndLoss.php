<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReportProfitAndLoss
 */
class GetReportProfitAndLoss extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Reports/ProfitAndLoss';
    }

    /**
     * @param  null|string  $fromDate  filter by the from date of the report e.g. 2021-02-01
     * @param  null|string  $toDate  filter by the to date of the report e.g. 2021-02-28
     * @param  null|int  $periods  The number of periods to compare (integer between 1 and 12)
     * @param  null|string  $timeframe  The period size to compare to (MONTH, QUARTER, YEAR)
     * @param  null|string  $trackingCategoryId  The trackingCategory 1 for the ProfitAndLoss report
     * @param  null|string  $trackingCategoryId2  The trackingCategory 2 for the ProfitAndLoss report
     * @param  null|string  $trackingOptionId  The tracking option 1 for the ProfitAndLoss report
     * @param  null|string  $trackingOptionId2  The tracking option 2 for the ProfitAndLoss report
     * @param  null|bool  $standardLayout  Return the standard layout for the ProfitAndLoss report
     * @param  null|bool  $paymentsOnly  Return cash only basis for the ProfitAndLoss report
     */
    public function __construct(
        protected ?string $fromDate = null,
        protected ?string $toDate = null,
        protected ?int $periods = null,
        protected ?string $timeframe = null,
        protected ?string $trackingCategoryId = null,
        protected ?string $trackingCategoryId2 = null,
        protected ?string $trackingOptionId = null,
        protected ?string $trackingOptionId2 = null,
        protected ?bool $standardLayout = null,
        protected ?bool $paymentsOnly = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'periods' => $this->periods,
            'timeframe' => $this->timeframe,
            'trackingCategoryID' => $this->trackingCategoryId,
            'trackingCategoryID2' => $this->trackingCategoryId2,
            'trackingOptionID' => $this->trackingOptionId,
            'trackingOptionID2' => $this->trackingOptionId2,
            'standardLayout' => $this->standardLayout,
            'paymentsOnly' => $this->paymentsOnly,
        ]);
    }
}
