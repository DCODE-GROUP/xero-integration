<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBankTransactionsHistory
 */
class GetBankTransactionsHistory extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/BankTransactions/{$this->bankTransactionId}/History";
	}


	/**
	 * @param string $bankTransactionId Xero generated unique identifier for a bank transaction
	 */
	public function __construct(
		protected string $bankTransactionId,
	) {
	}
}
