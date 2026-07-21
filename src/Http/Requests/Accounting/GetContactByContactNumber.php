<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getContactByContactNumber
 */
class GetContactByContactNumber extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Contacts/{$this->contactNumber}";
    }

    /**
     * @param  string  $contactNumber  This field is read only on the Xero contact screen, used to identify contacts in external systems (max length = 50).
     */
    public function __construct(
        protected string $contactNumber,
    ) {}
}
