<?php

namespace Dcodegroup\XeroIntegration\Http\Controllers;

use Dcodegroup\XeroIntegration\Events\XeroWebhookRecievedEvent;
use Dcodegroup\XeroIntegration\Http\Requests\XeroWebhookRequest;
use Dcodegroup\XeroIntegration\Jobs\XeroWebhookProcessJob;
use Dcodegroup\XeroIntegration\XeroApp;
use XeroPHP\Webhook as XeroWebHook;

class XeroWebhookController
{
    public function __invoke(XeroWebhookRequest $request)
    {
        $xeroWebhook = new XeroWebHook(app(XeroApp::class), (string) $request->getContent());

        XeroWebhookRecievedEvent::dispatch($request);

        XeroWebhookProcessJob::dispatchAfterResponse($xeroWebhook);

        return response(status: 201);
    }
}
