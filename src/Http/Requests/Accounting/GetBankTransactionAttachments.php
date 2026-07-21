<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBankTransactionAttachments
 */
class GetBankTransactionAttachments extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/BankTransactions/{$this->bankTransactionId}/Attachments";
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     */
    public function __construct(
        protected string $bankTransactionId,
    ) {}
}
