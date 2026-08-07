<?php

namespace DcodeGroup\XeroIntegration\Events;

use DcodeGroup\XeroIntegration\Data\XeroContactData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class XeroContactUpdatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroContactUpdatedEvent constructor.
     */
    public function __construct(public XeroContactData $data) {}
}
