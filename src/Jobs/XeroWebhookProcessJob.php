<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Models\XeroWebhook;

class XeroWebhookProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(protected XeroWebhook $webhook)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        foreach ($this->webhook->events as $event) {
            match ($event->event_category) {
                'CONTACT' => XeroWebhookContactProcessJob::dispatch($event),
                'INVOICE' => XeroWebhookInvoiceProcessJob::dispatch($event),
                'CREDITNOTE' => XeroWebhookCreditNoteProcessJob::dispatch($event),
                default => null,
            };
        }
    }
}
