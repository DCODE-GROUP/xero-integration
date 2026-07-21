<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getAccountAttachments
 */
class GetAccountAttachments extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Accounts/{$this->accountId}/Attachments";
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     */
    public function __construct(
        protected string $accountId,
    ) {}
}
