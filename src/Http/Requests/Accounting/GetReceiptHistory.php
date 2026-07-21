<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReceiptHistory
 */
class GetReceiptHistory extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Receipts/{$this->receiptId}/History";
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     */
    public function __construct(
        protected string $receiptId,
    ) {}
}
