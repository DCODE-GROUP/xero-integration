<?php

namespace Dcodegroup\XeroIntegration\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dcodegroup\XeroIntegration\XeroIntegrationService
 */
class XeroIntegrationService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Dcodegroup\XeroIntegration\XeroIntegrationService::class;
    }
}
