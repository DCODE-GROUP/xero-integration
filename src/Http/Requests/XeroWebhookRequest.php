<?php

namespace Dcodegroup\XeroIntegration\Http\Requests;

use Dcodegroup\XeroIntegration\XeroApp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use XeroPHP\Application;
use XeroPHP\Webhook;

class XeroWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tempApp = new Application('pending', 'pending', false);
        $tempApp->setConfig(
            [
                'webhook' => ['signing_key' => config('xero-integration.webhooks.secret')]
            ]
        );
        $webhook = new Webhook($tempApp, (string) $this->getContent());

        $signature = $this->header('X-Xero-Signature');

        if (empty($signature)) {
            Log::info("Empty sig");
            return false;
        }

        return $webhook->validate((string) $signature);
    }

    public function rules(): array
    {
        return [
            '*' => ['required', 'array'],
        ];
    }
}
