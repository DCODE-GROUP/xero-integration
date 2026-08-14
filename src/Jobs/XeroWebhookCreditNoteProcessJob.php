<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Events\XeroCreditNoteCreatedEvent;
use Dcodegroup\XeroIntegration\Events\XeroCreditNoteUpdatedEvent;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegration;
use XeroPHP\Models\Accounting\CreditNote as XeroCreditNote;
use XeroPHP\Webhook;
use XeroPHP\Webhook\Event;

/**
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(Event $event, Webhook $webhook)
 * @method static void dispatch(Event $event, Webhook $webhook)
 */
class XeroWebhookCreditNoteProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(
        protected Event $event,
        protected Webhook $webhook
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::CREDIT_NOTE->getModelClass());

        /** @var \Dcodegroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        /** @var ?XeroCreditNote $xeroCreditNote */
        $xeroCreditNote = $xero->find($this->event->getResourceId());

        if (empty($xeroCreditNote)) {
            $this->failed(new XeroIntegrationException("Xero Credit Note with ID {$this->event->getResourceId()} not found."));

            return;
        }

        match ($this->event->getEventType()) {
            'CREATE' => XeroCreditNoteCreatedEvent::dispatch($xeroCreditNote),
            'UPDATE' => XeroCreditNoteUpdatedEvent::dispatch($xeroCreditNote),
            default => null,
        };
    }
}
