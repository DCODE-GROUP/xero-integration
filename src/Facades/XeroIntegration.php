<?php

namespace DcodeGroup\XeroIntegration\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DcodeGroup\XeroIntegration\XeroIntegration
 */
class XeroIntegration extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \DcodeGroup\XeroIntegration\XeroIntegration::class;
    }
}
