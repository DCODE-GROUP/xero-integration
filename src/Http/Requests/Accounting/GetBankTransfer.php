<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBankTransfer
 */
class GetBankTransfer extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/BankTransfers/{$this->bankTransferId}";
	}


	/**
	 * @param string $bankTransferId Xero generated unique identifier for a bank transfer
	 */
	public function __construct(
		protected string $bankTransferId,
	) {
	}
}
