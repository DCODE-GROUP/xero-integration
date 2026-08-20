<?php

namespace Dcodegroup\XeroIntegration\Http\Controllers;

use Dcodegroup\XeroIntegration\Events\XeroWebhookRecievedEvent;
use Dcodegroup\XeroIntegration\Http\Requests\XeroWebhookRequest;
use Dcodegroup\XeroIntegration\Jobs\XeroWebhookProcessJob;
use Dcodegroup\XeroIntegration\Models\XeroWebhook as XeroWebhookModel;
use Dcodegroup\XeroIntegration\XeroApp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use XeroPHP\Application;
use XeroPHP\Webhook as XeroWebHook;

class XeroWebhookController
{
    public function __invoke(XeroWebhookRequest $request)
    {
        Log::info('Webhook received: ', $request->getContent());
        $tempApp = new Application('pending', 'pending', false);
        $xeroWebhook = new XeroWebHook($temp, (string) $request->getContent());

        $modelData = [
            'payload' => (string) $request->getContent(),
        ];
        if (config('xero-integration.tenancy.enabled')) {
            $modelData['tenant_id'] = null;
            $tenantId = $xeroWebhook->getEvents()->first()?->tenantId;
            $modelData['tenant_id'] = $tenantId;
        }
        $webhookModel = XeroWebhookModel::create($modelData);
        XeroWebhookRecievedEvent::dispatch($webhookModel);
        XeroWebhookProcessJob::dispatchAfterResponse($webhookModel);

        return response(status: 201);
    }
}
