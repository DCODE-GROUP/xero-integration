<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBrandingThemePaymentServices
 */
class GetBrandingThemePaymentServices extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/BrandingThemes/{$this->brandingThemeId}/PaymentServices";
	}


	/**
	 * @param string $brandingThemeId Unique identifier for a Branding Theme
	 */
	public function __construct(
		protected string $brandingThemeId,
	) {
	}
}
