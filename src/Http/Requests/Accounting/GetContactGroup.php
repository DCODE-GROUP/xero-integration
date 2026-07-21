<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getContactGroup
 */
class GetContactGroup extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/ContactGroups/{$this->contactGroupId}";
	}


	/**
	 * @param string $contactGroupId Unique identifier for a Contact Group
	 */
	public function __construct(
		protected string $contactGroupId,
	) {
	}
}
