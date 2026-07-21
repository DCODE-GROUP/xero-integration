<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBrandingThemes
 */
class GetBrandingThemes extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/BrandingThemes";
	}


	public function __construct()
	{
	}
}
