<?php

namespace Dcodegroup\XeroIntegration\Http\Controllers;

use Dcodegroup\XeroIntegration\Exceptions\UnauthorizedXero;
use Dcodegroup\XeroIntegration\Facades\XeroIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class XeroCallbackController extends Controller
{
    /**
     * @throws UnauthorizedXero
     */
    public function __invoke(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new UnauthorizedXero('Could not authorize Xero!');
        }

        $validated = $validator->validated();

        XeroIntegrationService::saveAccessTokenFromCode($validated['code']);

        return redirect()->to(config('xero-integration.routes.callback_success_route'));
    }
}
