<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deletePrepaymentAllocations
 */
class DeletePrepaymentAllocations extends Request
{
    protected Method $method = Method::DELETE;

    public function resolveEndpoint(): string
    {
        return "/Prepayments/{$this->prepaymentId}/Allocations/{$this->allocationId}";
    }

    /**
     * @param  string  $prepaymentId  Unique identifier for a PrePayment
     * @param  string  $allocationId  Unique identifier for Allocation object
     */
    public function __construct(
        protected string $prepaymentId,
        protected string $allocationId,
    ) {}
}
