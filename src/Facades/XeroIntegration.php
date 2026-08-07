<?php

namespace DcodeGroup\XeroIntegration\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \DcodeGroup\XeroIntegration\XeroIntegration make(\DcodeGroup\XeroIntegration\XeroApp $app, \DcodeGroup\XeroIntegration\XeroQuery $query)
 *
 * @see \DcodeGroup\XeroIntegration\XeroIntegration
 */
class XeroIntegration extends Facade
{
    /**
     * @return class-string<\DcodeGroup\XeroIntegration\XeroIntegration>
     */
    protected static function getFacadeAccessor(): string
    {
        return \DcodeGroup\XeroIntegration\XeroIntegration::class;
    }
}
