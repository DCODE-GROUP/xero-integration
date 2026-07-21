<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteTrackingOptions
 */
class DeleteTrackingOptions extends Request
{
    protected Method $method = Method::DELETE;

    public function resolveEndpoint(): string
    {
        return "/TrackingCategories/{$this->trackingCategoryId}/Options/{$this->trackingOptionId}";
    }

    /**
     * @param  string  $trackingCategoryId  Unique identifier for a TrackingCategory
     * @param  string  $trackingOptionId  Unique identifier for a Tracking Option
     */
    public function __construct(
        protected string $trackingCategoryId,
        protected string $trackingOptionId,
    ) {}
}
