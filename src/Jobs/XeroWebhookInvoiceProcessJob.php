<?php

namespace DcodeGroup\XeroIntegration\Jobs;

use DcodeGroup\XeroIntegration\Data\XeroInvoiceData;
use DcodeGroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use DcodeGroup\XeroIntegration\Events\XeroInvoiceCreatedEvent;
use DcodeGroup\XeroIntegration\Events\XeroInvoiceUpdatedEvent;
use DcodeGroup\XeroIntegration\Exceptions\XeroIntegrationException;
use DcodeGroup\XeroIntegration\Facades\XeroIntegration;
use Illuminate\Foundation\Bus\PendingDispatch;
use XeroPHP\Webhook\Event;

/**
 * @method static PendingDispatch dispatch(Event $event)
 * @method static void dispatchSync(Event $event)
 */
class XeroWebhookInvoiceProcessJob extends AbstractXeroWebhookJob
{
    public function __construct(protected Event $event)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $query = $this->xeroApp->load(XeroRelationshipsEnum::INVOICE->getModelClass());

        /** @var \DcodeGroup\XeroIntegration\XeroIntegration $xero */
        $xero = XeroIntegration::make($this->xeroApp, $query);

        $model = $xero->find($this->event->getResourceId());

        if (empty($model)) {
            report(new XeroIntegrationException("Xero Invoice with ID {$this->event->getResourceId()} not found."));

            return;
        }

        $data = XeroInvoiceData::fromXero($model);

        match ($this->event->getEventType()) {
            'CREATE' => XeroInvoiceCreatedEvent::dispatch($data),
            'UPDATE' => XeroInvoiceUpdatedEvent::dispatch($data),
            default => null,
        };
    }
}
