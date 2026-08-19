<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Enums\XeroWebhookStatusEnum;
use Dcodegroup\XeroIntegration\Models\XeroWebhookEvent;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @method static PendingDispatch dispatch(XeroWebhookEvent $event)
 * @method static void dispatch(XeroWebhookEvent $event)
 */
abstract class AbstractXeroWebhookEventJob extends AbstractXeroWebhookJob
{
    public function __construct(protected XeroWebhookEvent $event)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->event->setStatus(XeroWebhookStatusEnum::PROCESSING);
        $this->handleEvent();
        $this->event->setStatus(XeroWebhookStatusEnum::SUCCESSFUL);
    }

    public function fail($exception = null)
    {
        $this->event->setStatus(XeroWebhookStatusEnum::FAILURE);
    }

    abstract public function handleEvent(): void;
}
