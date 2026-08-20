<?php

namespace Dcodegroup\XeroIntegration\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Dcodegroup\XeroIntegration\XeroIntegration make(\Dcodegroup\XeroIntegration\XeroApp $app, \Dcodegroup\XeroIntegration\XeroQuery $query)
 * @method static \XeroPHP\Remote\Model|null find(void $guid)
 * @method static \Dcodegroup\XeroIntegration\XeroIntegration limit(int $limit)
 * @method static \Illuminate\Support\Collection|\XeroPHP\Remote\Model[] get()
 * @method static void firstOrFail()
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
