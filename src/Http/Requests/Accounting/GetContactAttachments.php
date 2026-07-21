<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getContactAttachments
 */
class GetContactAttachments extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Contacts/{$this->contactId}/Attachments";
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     */
    public function __construct(
        protected string $contactId,
    ) {}
}
