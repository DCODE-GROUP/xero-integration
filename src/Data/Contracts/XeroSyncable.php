<?php

namespace DcodeGroup\XeroIntegration\Data\Contracts;

interface XeroSyncable
{
    public function sendToXero(string $tenant): void;
}
