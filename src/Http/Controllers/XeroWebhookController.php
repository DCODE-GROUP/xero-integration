<?php

namespace Dcodegroup\XeroIntegration\Http\Controllers;

use Dcodegroup\XeroIntegration\Events\XeroWebhookRecievedEvent;
use Dcodegroup\XeroIntegration\Http\Requests\XeroWebhookRequest;
use Dcodegroup\XeroIntegration\Jobs\XeroWebhookProcessJob;
use Dcodegroup\XeroIntegration\Models\XeroWebhook as XeroWebhookModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use XeroPHP\Application;
use XeroPHP\Webhook as XeroWebHook;

class XeroWebhookController
{
    public function __invoke(XeroWebhookRequest $request)
    {
        $tempApp = new Application('pending', 'pending', false);
        $tempApp->setConfig([
            'webhook' => ['signing_key' => config('xero-integration.webhooks.secret')],
        ]);
        $xeroWebhook = new XeroWebHook($tempApp, (string) $request->getContent());

        $modelData = [
            'payload' => (string) $request->getContent(),
        ];
        if (config('xero-integration.tenancy.enabled')) {
            $modelData['tenant_id']  = $xeroWebhook->getEvents()->first()?->tenantId;
        }
        $webhookModel = XeroWebhookModel::create($modelData);
        XeroWebhookRecievedEvent::dispatch($webhookModel);
        XeroWebhookProcessJob::dispatchAfterResponse($webhookModel);

        return response(status: 200);
    }
}
