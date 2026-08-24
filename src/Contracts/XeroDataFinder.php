<?php

namespace Dcodegroup\XeroIntegration\Contracts;

use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use XeroPHP\Remote\Model;

interface XeroDataFinder
{
    /**
     * Find a Xero record for a data class by its Xero identifier.
     *
     * @param  class-string<AbstractXeroData>  $dataClass
     */
    public function find(string $dataClass, string $xeroId): ?Model;
}
