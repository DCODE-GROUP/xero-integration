<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteContactGroupContacts
 */
class DeleteContactGroupContacts extends Request
{
    protected Method $method = Method::DELETE;

    public function resolveEndpoint(): string
    {
        return "/ContactGroups/{$this->contactGroupId}/Contacts";
    }

    /**
     * @param  string  $contactGroupId  Unique identifier for a Contact Group
     */
    public function __construct(
        protected string $contactGroupId,
    ) {}
}
