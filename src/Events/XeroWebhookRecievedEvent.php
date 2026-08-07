<?php

namespace Dcodegroup\XeroIntegration\Events;

use Dcodegroup\XeroIntegration\Http\Requests\XeroWebhookRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class XeroWebhookRecievedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroWebhookRecievedEvent constructor.
     */
    public function __construct(public XeroWebhookRequest $request) {}
}
