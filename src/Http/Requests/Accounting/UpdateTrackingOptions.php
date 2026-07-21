<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateTrackingOptions
 */
class UpdateTrackingOptions extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/TrackingCategories/{$this->trackingCategoryId}/Options/{$this->trackingOptionId}";
    }

    /**
     * @param  string  $trackingCategoryId  Unique identifier for a TrackingCategory
     * @param  string  $trackingOptionId  Unique identifier for a Tracking Option
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $trackingCategoryId,
        protected string $trackingOptionId,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
