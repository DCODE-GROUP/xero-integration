<?php

namespace Dcodegroup\XeroIntegration\Services;

use Dcodegroup\XeroIntegration\Contracts\XeroDataFinder;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\XeroApp;
use Dcodegroup\XeroIntegration\XeroIntegration;
use Dcodegroup\XeroIntegration\XeroQuery;
use XeroPHP\Remote\Collection;
use XeroPHP\Remote\Model;

class XeroDataQueryService implements XeroDataFinder
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

    public function list(string $dataClass, string $resultClass): Collection
    {
        return XeroIntegration::make($this->xeroApp, $this->query($dataClass, $resultClass))->execute();
    }

    public function query(string $dataClass, string $resultClass): XeroQuery
    {
        $query = new XeroQuery($this->xeroApp, $resultClass);

        return $query->from($dataClass);
    }
}
