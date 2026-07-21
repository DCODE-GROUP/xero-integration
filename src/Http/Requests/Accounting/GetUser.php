<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getUser
 */
class GetUser extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Users/{$this->userId}";
	}


	/**
	 * @param string $userId Unique identifier for a User
	 */
	public function __construct(
		protected string $userId,
	) {
	}
}
