<?php

namespace Dcodegroup\XeroIntegration\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use XeroPHP\Webhook\Event;

class XeroWebhookProcessingFailedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroWebhookProcessingFailedEvent constructor.
     */
    public function __construct(
        public Event $webhook,
        public string $message
    ) {}
}
