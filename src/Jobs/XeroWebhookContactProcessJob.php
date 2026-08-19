<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Data\XeroContactData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Events\XeroContactCreatedEvent;
use Dcodegroup\XeroIntegration\Events\XeroContactUpdatedEvent;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegration;

class XeroWebhookContactProcessJob extends AbstractXeroWebhookEventJob
{
    public function handleEvent(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::CONTACT->getModelClass());

        /** @var \Dcodegroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        $model = $xero->find($this->event->resource_id);

        if (empty($model)) {
            report(new XeroIntegrationException("Xero Contact with ID {$this->event->resource_id} not found."));

            return;
        }

        $data = XeroContactData::fromXero($model);

        match ($this->event->event_type) {
            'CREATE' => XeroContactCreatedEvent::dispatch($data),
            'UPDATE' => XeroContactUpdatedEvent::dispatch($data),
            default => null,
        };
    }
}
