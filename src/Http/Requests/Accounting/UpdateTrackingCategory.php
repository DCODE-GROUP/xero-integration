<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateTrackingCategory
 */
class UpdateTrackingCategory extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/TrackingCategories/{$this->trackingCategoryId}";
    }

    /**
     * @param  string  $trackingCategoryId  Unique identifier for a TrackingCategory
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $trackingCategoryId,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
