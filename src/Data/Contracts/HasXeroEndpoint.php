<?php

namespace Dcodegroup\XeroIntegration\Data\Contracts;

use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;

/**
 * Marks a data type as migrated to direct API access.
 */
interface HasXeroEndpoint
{
    public static function getXeroEndpoint(): XeroEndpoint;

    /**
     * @param  array<string, mixed>  $payload  A record as returned by Xero, with dates already normalised.
     */
    public static function fromXeroArray(array $payload): self;
}
