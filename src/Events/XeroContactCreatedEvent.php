<?php

namespace DcodeGroup\XeroIntegration\Events;

use DcodeGroup\XeroIntegration\Data\XeroContactData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class XeroContactCreatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroContactCreatedEvent constructor.
     */
    public function __construct(public XeroContactData $data) {}
}
