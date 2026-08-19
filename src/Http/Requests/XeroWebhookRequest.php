<?php

namespace Dcodegroup\XeroIntegration\Http\Requests;

use Dcodegroup\XeroIntegration\XeroApp;
use Illuminate\Foundation\Http\FormRequest;
use XeroPHP\Webhook;

class XeroWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        $xeroApp = app(XeroApp::class);

        $webhook = new Webhook($xeroApp, (string) $this->getContent());

        $signature = $this->header('X-Xero-Signature');

        if (empty($signature)) {
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
