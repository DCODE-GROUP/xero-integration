<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getInvoice
 */
class GetInvoice extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Invoices/{$this->invoiceId}";
	}


	/**
	 * @param string $invoiceId Unique identifier for an Invoice
	 * @param null|int $unitdp e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
	 */
	public function __construct(
		protected string $invoiceId,
		protected ?int $unitdp = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['unitdp' => $this->unitdp]);
	}
}
