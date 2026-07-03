<?php

namespace DcodeGroup\XeroIntegration\Http\Controllers;

use DcodeGroup\XeroIntegration\Facades\XeroIntegrationService;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class XeroAuthController extends Controller
{
    public function __invoke(): Response
    {
        return redirect()->to(XeroIntegrationService::getAuthUrl());
    }
}
