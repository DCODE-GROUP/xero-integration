<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteContactGroupContact
 */
class DeleteContactGroupContact extends Request
{
    protected Method $method = Method::DELETE;

    public function resolveEndpoint(): string
    {
        return "/ContactGroups/{$this->contactGroupId}/Contacts/{$this->contactId}";
    }

    /**
     * @param  string  $contactGroupId  Unique identifier for a Contact Group
     * @param  string  $contactId  Unique identifier for a Contact
     */
    public function __construct(
        protected string $contactGroupId,
        protected string $contactId,
    ) {}
}
