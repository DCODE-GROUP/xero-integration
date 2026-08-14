<?php

namespace Dcodegroup\XeroIntegration\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
