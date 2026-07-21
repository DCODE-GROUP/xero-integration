<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * createBankTransferHistoryRecord
 */
class CreateBankTransferHistoryRecord extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return "/BankTransfers/{$this->bankTransferId}/History";
    }

    /**
     * @param  string  $bankTransferId  Xero generated unique identifier for a bank transfer
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $bankTransferId,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
