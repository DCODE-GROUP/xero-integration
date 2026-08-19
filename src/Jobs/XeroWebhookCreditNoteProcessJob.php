<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Data\XeroCreditNoteData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Events\XeroCreditNoteCreatedEvent;
use Dcodegroup\XeroIntegration\Events\XeroCreditNoteUpdatedEvent;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegration;

class XeroWebhookCreditNoteProcessJob extends AbstractXeroWebhookEventJob
{
    public function handleEvent(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::CREDIT_NOTE->getModelClass());

        /** @var \Dcodegroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        $model = $xero->find($this->event->resource_id);

        if (empty($model)) {
            $this->failed(new XeroIntegrationException("Xero Credit Note with ID {$this->event->resource_id} not found."));
            return;
        }

        $data = XeroCreditNoteData::fromXero($model);

        match ($this->event->event_type) {
            'CREATE' => XeroCreditNoteCreatedEvent::dispatch($data),
            'UPDATE' => XeroCreditNoteUpdatedEvent::dispatch($data),
            default => null,
        };
    }
}
