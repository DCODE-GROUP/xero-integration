<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getContact
 */
class GetContact extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Contacts/{$this->contactId}";
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     */
    public function __construct(
        protected string $contactId,
    ) {}
}
