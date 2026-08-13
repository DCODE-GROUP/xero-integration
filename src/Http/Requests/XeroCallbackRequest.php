<?php

namespace Dcodegroup\XeroIntegration\Http\Requests;

use Dcodegroup\XeroIntegration\XeroApp;
use Illuminate\Foundation\Http\FormRequest;
use XeroPHP\Webhook;

class XeroCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            '*' => ['required', 'array'],
        ];
    }
}
