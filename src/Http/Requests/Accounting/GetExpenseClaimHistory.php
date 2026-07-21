<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getExpenseClaimHistory
 */
class GetExpenseClaimHistory extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/ExpenseClaims/{$this->expenseClaimId}/History";
    }

    /**
     * @param  string  $expenseClaimId  Unique identifier for a ExpenseClaim
     */
    public function __construct(
        protected string $expenseClaimId,
    ) {}
}
