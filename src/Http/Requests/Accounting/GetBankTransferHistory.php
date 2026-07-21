<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBankTransferHistory
 */
class GetBankTransferHistory extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/BankTransfers/{$this->bankTransferId}/History";
    }

    /**
     * @param  string  $bankTransferId  Xero generated unique identifier for a bank transfer
     */
    public function __construct(
        protected string $bankTransferId,
    ) {}
}
