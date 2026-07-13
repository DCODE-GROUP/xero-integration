<?php

namespace DcodeGroup\XeroIntegration\Data\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use XeroPHP\Remote\Collection as XeroCollection;

interface HasXeroData
{
    public function fromModel(Model $model): self;

    public static function toCollection(array $items): ?Collection;

    public static function toXeroCollection(Collection $items): ?XeroCollection;
}
