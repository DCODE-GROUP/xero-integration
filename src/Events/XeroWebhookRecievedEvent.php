<?php

namespace Dcodegroup\XeroIntegration\Events;

use Dcodegroup\XeroIntegration\Models\XeroWebhook;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class XeroWebhookRecievedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroWebhookRecievedEvent constructor.
     */
    public function __construct(public XeroWebhook $request) {}
}
