<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getInvoiceReminders
 */
class GetInvoiceReminders extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/InvoiceReminders/Settings";
	}


	public function __construct()
	{
	}
}
