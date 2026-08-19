<?php

namespace Dcodegroup\XeroIntegration\Data\Contracts;

use Illuminate\Support\Collection;
use XeroPHP\Remote\Collection as XeroCollection;

interface HasXeroData
{
    public static function toCollection(array|XeroCollection|null $items): ?Collection;

    public static function toXeroCollection(Collection $items): ?array;
}
