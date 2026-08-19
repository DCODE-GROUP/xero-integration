<?php

namespace Dcodegroup\XeroIntegration\Http\Controllers;

use Dcodegroup\XeroIntegration\Facades\XeroIntegrationService;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class XeroAuthController extends Controller
{
    public function __invoke(): Response
    {
        return redirect()->to(XeroIntegrationService::getAuthUrl());
    }
}
