<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getTrackingCategory
 */
class GetTrackingCategory extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/TrackingCategories/{$this->trackingCategoryId}";
    }

    /**
     * @param  string  $trackingCategoryId  Unique identifier for a TrackingCategory
     */
    public function __construct(
        protected string $trackingCategoryId,
    ) {}
}
