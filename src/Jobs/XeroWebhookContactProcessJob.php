<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Events\XeroContactCreatedEvent;
use Dcodegroup\XeroIntegration\Events\XeroContactUpdatedEvent;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegration;
use XeroPHP\Models\Accounting\Contact;

class XeroWebhookContactProcessJob extends AbstractXeroWebhookEventJob
{
    public function handleEvent(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::CONTACT->getModelClass());

        /** @var \Dcodegroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        /** @var Contact|null $model */
        $model = $xero->find($this->event->resource_id);

        if (empty($model)) {
            $this->failed(new XeroIntegrationException("Xero Contact with ID {$this->event->resource_id} not found."));

            return;
        }

        match ($this->event->event_type) {
            'CREATE' => XeroContactCreatedEvent::dispatch($model),
            'UPDATE' => XeroContactUpdatedEvent::dispatch($model),
            default => null,
        };
    }
}
