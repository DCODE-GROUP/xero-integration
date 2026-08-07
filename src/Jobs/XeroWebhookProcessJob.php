<?php

namespace DcodeGroup\XeroIntegration\Jobs;

use XeroPHP\Webhook;

class XeroWebhookProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(protected Webhook $webhook)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        foreach ($this->webhook->getEvents() as $event) {
            match ($event['eventCategory']) {
                'CONTACT' => XeroWebhookContactProcessJob::dispatch($event),
                'INVOICE' => XeroWebhookInvoiceProcessJob::dispatch($event),
                'CREDITNOTE' => XeroWebhookCreditNoteProcessJob::dispatch($event),
                default => null,
            };
        }
    }
}
