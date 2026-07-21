<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getReceipt
 */
class GetReceipt extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Receipts/{$this->receiptId}";
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     * @param  null|int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     */
    public function __construct(
        protected string $receiptId,
        protected ?int $unitdp = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['unitdp' => $this->unitdp]);
    }
}
