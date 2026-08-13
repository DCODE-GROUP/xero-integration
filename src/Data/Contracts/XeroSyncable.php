<?php

namespace Dcodegroup\XeroIntegration\Data\Contracts;

use Illuminate\Database\Eloquent\Model;

interface XeroSyncable
{
    public function sendToXero(): void;
}
