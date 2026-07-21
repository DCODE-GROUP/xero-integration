<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getInvoiceAsPdf
 */
class GetInvoiceAsPdf extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Invoices/{$this->invoiceId}/pdf";
	}


	/**
	 * @param string $invoiceId Unique identifier for an Invoice
	 */
	public function __construct(
		protected string $invoiceId,
	) {
	}
}
