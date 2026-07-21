<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getContactCISSettings
 */
class GetContactCissettings extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Contacts/{$this->contactId}/CISSettings";
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     */
    public function __construct(
        protected string $contactId,
    ) {}
}
