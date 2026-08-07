<?php

namespace DcodeGroup\XeroIntegration\Jobs;

use DcodeGroup\XeroIntegration\Data\XeroContactData;
use DcodeGroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use DcodeGroup\XeroIntegration\Events\XeroContactCreatedEvent;
use DcodeGroup\XeroIntegration\Events\XeroContactUpdatedEvent;
use DcodeGroup\XeroIntegration\Exceptions\XeroIntegrationException;
use DcodeGroup\XeroIntegration\Facades\XeroIntegration;
use Illuminate\Foundation\Bus\PendingDispatch;
use XeroPHP\Webhook\Event;

/**
 * @method static PendingDispatch dispatch(Event $event)
 * @method static void dispatch(Event $event)
 */
class XeroWebhookContactProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(protected Event $event)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::CONTACT->getModelClass());

        /** @var \DcodeGroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        $model = $xero->find($this->event->getResourceId());

        if (empty($model)) {
            report(new XeroIntegrationException("Xero Contact with ID {$this->event->getResourceId()} not found."));

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
