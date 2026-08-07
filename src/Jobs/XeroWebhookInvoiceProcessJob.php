<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Data\XeroInvoiceData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Events\XeroInvoiceCreatedEvent;
use Dcodegroup\XeroIntegration\Events\XeroInvoiceUpdatedEvent;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegration;
use Illuminate\Foundation\Bus\PendingDispatch;
use XeroPHP\Webhook\Event;

/**
 * @method static PendingDispatch dispatch(Event $event)
 * @method static void dispatchSync(Event $event)
 */
class XeroWebhookInvoiceProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(protected Event $event)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::INVOICE->getModelClass());

        /** @var \Dcodegroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        $model = $xero->find($this->event->getResourceId());

        if (empty($model)) {
            report(new XeroIntegrationException("Xero Invoice with ID {$this->event->getResourceId()} not found."));

            return;
        }

        $data = XeroInvoiceData::fromXero($model);

        match ($this->event->getEventType()) {
            'CREATE' => XeroInvoiceCreatedEvent::dispatch($data),
            'UPDATE' => XeroInvoiceUpdatedEvent::dispatch($data),
            default => null,
        };
    }
}
