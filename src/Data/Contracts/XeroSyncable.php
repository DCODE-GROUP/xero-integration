<?php

namespace DcodeGroup\XeroIntegration\Data\Contracts;

use Illuminate\Database\Eloquent\Model;
use XeroPHP\Remote\Model as XeroModel;

interface XeroSyncable
{
    public function toXeroArray(): array;

    public static function fromModel(Model $model): self;

    public static function fromXero(XeroModel $xeroObject): self;

    public function syncToModel(): Model;

    public function localModel(): ?Model;
}
