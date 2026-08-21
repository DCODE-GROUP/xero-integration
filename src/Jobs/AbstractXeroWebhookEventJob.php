<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Enums\XeroWebhookStatusEnum;
use Dcodegroup\XeroIntegration\Facades\XeroIntegrationService;
use Dcodegroup\XeroIntegration\Models\XeroWebhookEvent;
use Dcodegroup\XeroIntegration\XeroApp;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @method static PendingDispatch dispatch(XeroWebhookEvent $event)
 * @method static void dispatch(XeroWebhookEvent $event)
 */
abstract class AbstractXeroWebhookEventJob extends AbstractXeroWebhookJob
{
    protected XeroApp $xeroApp;

    public function __construct(protected XeroWebhookEvent $event)
    {
        parent::__construct();
        if (config('xero-integration.tenancy.enabled')) {
            $currentTenant = XeroIntegrationService::getApplicationTenant()?->getKey();
            $tenantId = $currentTenant ? $currentTenant->id : session(config('xero-integration.tenancy.session_name'));
            if ($tenantId) {
                session([config('xero-integration.tenancy.session_name') => $tenantId]);
            }
        }
        $this->xeroApp = app(XeroApp::class);
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
