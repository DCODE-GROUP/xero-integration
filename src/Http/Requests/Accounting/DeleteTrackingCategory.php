<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteTrackingCategory
 */
class DeleteTrackingCategory extends Request
{
	protected Method $method = Method::DELETE;


	public function resolveEndpoint(): string
	{
		return "/TrackingCategories/{$this->trackingCategoryId}";
	}


	/**
	 * @param string $trackingCategoryId Unique identifier for a TrackingCategory
	 */
	public function __construct(
		protected string $trackingCategoryId,
	) {
	}
}
