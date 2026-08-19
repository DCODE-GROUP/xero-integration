<?php

namespace Dcodegroup\XeroIntegration\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Dcodegroup\XeroIntegration\XeroIntegration make(\Dcodegroup\XeroIntegration\XeroApp $app, \Dcodegroup\XeroIntegration\XeroQuery $query)
 *
 * @see \Dcodegroup\XeroIntegration\XeroIntegration
 */
class XeroIntegration extends Facade
{
    /**
     * @return class-string<\Dcodegroup\XeroIntegration\XeroIntegration>
     */
    protected static function getFacadeAccessor(): string
    {
        return \Dcodegroup\XeroIntegration\XeroIntegration::class;
    }
}
