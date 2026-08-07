<?php

namespace Dcodegroup\XeroIntegration\Events;

use Dcodegroup\XeroIntegration\Data\XeroContactData;
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
