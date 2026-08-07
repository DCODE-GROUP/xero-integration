<?php

namespace Dcodegroup\XeroIntegration\Data\Contracts;

interface XeroSyncable
{
    public function sendToXero(string $tenant): void;
}
