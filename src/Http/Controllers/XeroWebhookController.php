<?php

namespace DcodeGroup\XeroIntegration\Http\Controllers;

use DcodeGroup\XeroIntegration\Events\XeroWebhookRecievedEvent;
use DcodeGroup\XeroIntegration\Http\Requests\XeroWebhookRequest;
use DcodeGroup\XeroIntegration\Jobs\XeroWebhookProcessJob;
use DcodeGroup\XeroIntegration\XeroApp;
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
