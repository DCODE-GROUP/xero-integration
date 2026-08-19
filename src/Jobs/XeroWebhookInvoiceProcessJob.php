<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Events\XeroInvoiceCreatedEvent;
use Dcodegroup\XeroIntegration\Events\XeroInvoiceUpdatedEvent;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegration;
use XeroPHP\Models\Accounting\Invoice;

class XeroWebhookInvoiceProcessJob extends AbstractXeroWebhookEventJob
{
    public function handleEvent(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::INVOICE->getModelClass());

        /** @var \Dcodegroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        /** @var Invoice|null $model */
        $model = $xero->find($this->event->resource_id);

        if (empty($model)) {
            $this->failed(new XeroIntegrationException("Xero Invoice with ID {$this->event->resource_id} not found."));

            return;
        }

        match ($this->event->event_type) {
            'CREATE' => XeroInvoiceCreatedEvent::dispatch($model),
            'UPDATE' => XeroInvoiceUpdatedEvent::dispatch($model),
            default => null,
        };
    }
}
