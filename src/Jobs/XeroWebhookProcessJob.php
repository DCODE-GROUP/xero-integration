<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use XeroPHP\Webhook;

class XeroWebhookProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(protected Webhook $webhook)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        try {
            foreach ($this->webhook->getEvents() as $event) {

                match (data_get($event, 'eventCategory')) {
                    'CONTACT' => XeroWebhookContactProcessJob::dispatch($event, $this->webhook),
                    'INVOICE' => XeroWebhookInvoiceProcessJob::dispatch($event, $this->webhook),
                    'CREDITNOTE' => XeroWebhookCreditNoteProcessJob::dispatch($event, $this->webhook),
                    default => null,
                };
            }
        } catch (\Throwable $th) {
            $this->failed($th);
        }
    }
}
