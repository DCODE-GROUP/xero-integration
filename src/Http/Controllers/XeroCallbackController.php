<?php

namespace Dcodegroup\XeroIntegration\Http\Controllers;

use Dcodegroup\XeroIntegration\Exceptions\UnauthorizedXero;
use Dcodegroup\XeroIntegration\Facades\XeroIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class XeroCallbackController extends Controller
{
    /**
     * @throws UnauthorizedXero
     */
    public function __invoke(Request $request): Response
    {
        // Validate here so that failure does not return 429 and can record error.
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string'],
            'state' => ['required', Rule::in([config('xero-integration.oauth.state')])],
        ]);

        if ($validator->fails()) {
            throw new UnauthorizedXero('Could not authorize Xero!');
        }

        $validated = $validator->validated();

        $xeroToken = XeroIntegrationService::saveAccessTokenFromCode($validated['code']);

        XeroIntegrationService::setXeroTenant($xeroToken);

        return redirect()->to(Session::get(config('xero-integration.routes.success_url_session_name')));
    }
}
