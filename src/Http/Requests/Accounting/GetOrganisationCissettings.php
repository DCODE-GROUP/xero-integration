<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getOrganisationCISSettings
 */
class GetOrganisationCissettings extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Organisation/{$this->organisationId}/CISSettings";
    }

    /**
     * @param  string  $organisationId  The unique Xero identifier for an organisation
     */
    public function __construct(
        protected string $organisationId,
    ) {}
}
