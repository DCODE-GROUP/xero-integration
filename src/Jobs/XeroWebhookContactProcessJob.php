<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Events\XeroContactCreatedEvent;
use Dcodegroup\XeroIntegration\Events\XeroContactUpdatedEvent;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegration;
use Illuminate\Foundation\Bus\PendingDispatch;
use XeroPHP\Models\Accounting\Contact as XeroContact;
use XeroPHP\Webhook;
use XeroPHP\Webhook\Event;

/**
 * @method static PendingDispatch dispatch(Event $event, Webhook $webhook)
 * @method static void dispatch(Event $event, Webhook $webhook)
 */
class XeroWebhookContactProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(
        protected Event $event,
        protected Webhook $webhook
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::CONTACT->getModelClass());

        /** @var \Dcodegroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        /** @var ?XeroContact $xeroContact */
        $xeroContact = $xero->find($this->event->getResourceId());

        if (empty($xeroContact)) {
            $this->failed(new XeroIntegrationException("Xero Contact with ID {$this->event->getResourceId()} not found."));

            return;
        }

        match ($this->event->getEventType()) {
            'CREATE' => XeroContactCreatedEvent::dispatch($xeroContact),
            'UPDATE' => XeroContactUpdatedEvent::dispatch($xeroContact),
            default => null,
        };
    }
}
