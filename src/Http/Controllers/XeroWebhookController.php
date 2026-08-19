<?php

namespace Dcodegroup\XeroIntegration\Http\Controllers;

use Dcodegroup\XeroIntegration\Events\XeroWebhookRecievedEvent;
use Dcodegroup\XeroIntegration\Http\Requests\XeroWebhookRequest;
use Dcodegroup\XeroIntegration\Jobs\XeroWebhookProcessJob;
use Dcodegroup\XeroIntegration\Models\XeroWebhook as XeroWebhookModel;
use Dcodegroup\XeroIntegration\XeroApp;
use Illuminate\Support\Facades\Session;
use XeroPHP\Webhook as XeroWebHook;

class XeroWebhookController
{
    public function __invoke(XeroWebhookRequest $request)
    {
        $xeroWebhook = new XeroWebHook(app(XeroApp::class), (string) $request->getContent());

        $modelData = [
            'payload' => (string) $request->getContent(),
        ];
        if (config('xero-integration.tenancy.enabled')) {
            $modelData['tenant_id'] = null;
            $sessionName = config('xero-integration.tenancy.session_name');
            if (! empty($sessionName) && Session::has($sessionName)) {
                $tenantId = Session::get($sessionName);
                $modelData['tenant_id'] = $tenantId;
            }
        }
        $webhookModel = XeroWebhookModel::create($modelData);
        XeroWebhookRecievedEvent::dispatch($webhookModel);
        XeroWebhookProcessJob::dispatchAfterResponse($webhookModel);

        return response(status: 201);
    }
}
