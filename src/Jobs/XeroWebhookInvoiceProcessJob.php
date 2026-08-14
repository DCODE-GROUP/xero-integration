<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Events\XeroInvoiceCreatedEvent;
use Dcodegroup\XeroIntegration\Events\XeroInvoiceUpdatedEvent;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegration;
use Illuminate\Foundation\Bus\PendingDispatch;
use XeroPHP\Models\Accounting\Invoice as XeroInvoice;
use XeroPHP\Webhook;
use XeroPHP\Webhook\Event;

/**
 * @method static PendingDispatch dispatch(Event $event, Webhook $webhook)
 * @method static void dispatchSync(Event $event, Webhook $webhook)
 */
class XeroWebhookInvoiceProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(
        protected Event $event,
        protected Webhook $webhook
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::INVOICE->getModelClass());

        /** @var \Dcodegroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        /** @var ?XeroInvoice $xeroInvoice */
        $xeroInvoice = $xero->find($this->event->getResourceId());

        if (empty($xeroInvoice)) {
            $this->failed(new XeroIntegrationException("Xero Invoice with ID {$this->event->getResourceId()} not found."));

            return;
        }

        match ($this->event->getEventType()) {
            'CREATE' => XeroInvoiceCreatedEvent::dispatch($xeroInvoice),
            'UPDATE' => XeroInvoiceUpdatedEvent::dispatch($xeroInvoice),
            default => null,
        };
    }
}
