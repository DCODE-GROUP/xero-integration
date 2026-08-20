<?php

namespace Dcodegroup\XeroIntegration\Http\Controllers;

use Dcodegroup\XeroIntegration\Events\XeroWebhookRecievedEvent;
use Dcodegroup\XeroIntegration\Http\Requests\XeroWebhookRequest;
use Dcodegroup\XeroIntegration\Jobs\XeroWebhookProcessJob;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Dcodegroup\XeroIntegration\Models\XeroWebhook as XeroWebhookModel;
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
            $tenantId = collect($xeroWebhook->getEvents())->first()?->getTenantId();
            if ($tenantId) {
                $currentToken = XeroToken::withoutGlobalScopes()->where('current_tenant_id', $tenantId)->orderBy('updated_at', 'DESC')->first();

                $modelData['tenant_id'] = $currentToken?->tenant_id;
            }
        }
        $webhookModel = XeroWebhookModel::create($modelData);
        XeroWebhookRecievedEvent::dispatch($webhookModel);
        XeroWebhookProcessJob::dispatchAfterResponse($webhookModel);

        return response(status: 200);
    }
}
