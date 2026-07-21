<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBrandingTheme
 */
class GetBrandingTheme extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/BrandingThemes/{$this->brandingThemeId}";
	}


	/**
	 * @param string $brandingThemeId Unique identifier for a Branding Theme
	 */
	public function __construct(
		protected string $brandingThemeId,
	) {
	}
}
