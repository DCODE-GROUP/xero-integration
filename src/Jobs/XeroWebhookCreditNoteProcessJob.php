<?php

namespace DcodeGroup\XeroIntegration\Jobs;

use DcodeGroup\XeroIntegration\Data\XeroCreditNoteData;
use DcodeGroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use DcodeGroup\XeroIntegration\Events\XeroCreditNoteCreatedEvent;
use DcodeGroup\XeroIntegration\Events\XeroCreditNoteUpdatedEvent;
use DcodeGroup\XeroIntegration\Exceptions\XeroIntegrationException;
use DcodeGroup\XeroIntegration\Facades\XeroIntegration;
use XeroPHP\Webhook\Event;

/**
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(Event $event)
 * @method static void dispatch(Event $event)
 */
class XeroWebhookCreditNoteProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(protected Event $event)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::CREDIT_NOTE->getModelClass());

        /** @var \DcodeGroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        $model = $xero->find($this->event->getResourceId());

        if (empty($model)) {
            report(new XeroIntegrationException("Xero Credit Note with ID {$this->event->getResourceId()} not found."));

            return;
        }

        $data = XeroCreditNoteData::fromXero($model);

        match ($this->event->getEventType()) {
            'CREATE' => XeroCreditNoteCreatedEvent::dispatch($data),
            'UPDATE' => XeroCreditNoteUpdatedEvent::dispatch($data),
            default => null,
        };
    }
}
