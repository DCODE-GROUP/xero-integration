<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Events\XeroCreditNoteCreatedEvent;
use Dcodegroup\XeroIntegration\Events\XeroCreditNoteUpdatedEvent;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegration;
use XeroPHP\Models\Accounting\CreditNote;

class XeroWebhookCreditNoteProcessJob extends AbstractXeroWebhookEventJob
{
    public function handleEvent(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::CREDIT_NOTE->getModelClass());

        /** @var \Dcodegroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        /** @var CreditNote|null $model */
        $model = $xero->find($this->event->resource_id);

        if (empty($model)) {
            $this->failed(new XeroIntegrationException("Xero Credit Note with ID {$this->event->resource_id} not found."));

            return;
        }

        match ($this->event->event_type) {
            'CREATE' => XeroCreditNoteCreatedEvent::dispatch($model),
            'UPDATE' => XeroCreditNoteUpdatedEvent::dispatch($model),
            default => null,
        };
    }
}
