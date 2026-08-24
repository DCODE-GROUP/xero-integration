<?php

namespace Dcodegroup\XeroIntegration\Services;

use Dcodegroup\XeroIntegration\Contracts\XeroDataFinder;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\XeroApp;
use Dcodegroup\XeroIntegration\XeroIntegration;
use XeroPHP\Remote\Model;

class XeroDataFinderService implements XeroDataFinder
{
    public function __construct(
        protected XeroApp $xeroApp,
    ) {}

    /**
     * @param  class-string<AbstractXeroData>  $dataClass
     */
    public function find(string $dataClass, string $xeroId): ?Model
    {
        $query = $this->xeroApp->load($dataClass::getXeroRelationship()->getModelClass());

        return XeroIntegration::make($this->xeroApp, $query)->find($xeroId);
    }
}
