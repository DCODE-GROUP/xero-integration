<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteAccount
 */
class DeleteAccount extends Request
{
    protected Method $method = Method::DELETE;

    public function resolveEndpoint(): string
    {
        return "/Accounts/{$this->accountId}";
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     */
    public function __construct(
        protected string $accountId,
    ) {}
}
