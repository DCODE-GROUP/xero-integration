<?php

namespace Dcodegroup\XeroIntegration\Events;

use Dcodegroup\XeroIntegration\Models\XeroWebhook;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class XeroWebhookProcessingFailedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroWebhookProcessingFailedEvent constructor.
     */
    public function __construct(
        public XeroWebhook $webhook,
        public string $message
    ) {}
}
