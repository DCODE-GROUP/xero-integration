<?php

namespace DcodeGroup\XeroIntegration\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DcodeGroup\XeroIntegration\XeroIntegrationService
 */
class XeroIntegrationService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \DcodeGroup\XeroIntegration\XeroIntegrationService::class;
    }
}
