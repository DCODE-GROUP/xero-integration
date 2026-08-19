<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Data\XeroContactData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Events\XeroContactCreatedEvent;
use Dcodegroup\XeroIntegration\Events\XeroContactUpdatedEvent;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegration;
use Dcodegroup\XeroIntegration\Models\XeroWebhookEvent;
use Illuminate\Foundation\Bus\PendingDispatch;
use XeroPHP\Webhook\Event;

/**
 * @method static PendingDispatch dispatch(Event $event)
 * @method static void dispatch(Event $event)
 */
class XeroWebhookContactProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(protected XeroWebhookEvent $event)
    {
        parent::__construct();
    }

    public function handle(): void
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

        match ($this->event->getEventType()) {
            'CREATE' => XeroContactCreatedEvent::dispatch($data),
            'UPDATE' => XeroContactUpdatedEvent::dispatch($data),
            default => null,
        };
    }
}
