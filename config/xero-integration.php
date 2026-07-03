<?php

use DcodeGroup\XeroIntegration\Http\Controllers\XeroAuthController;
use DcodeGroup\XeroIntegration\Http\Controllers\XeroCallbackController;

// config for DcodeGroup/XeroIntegration
return [
    'tenancy' => [
        'enabled' => env('XERO_TENANCY_ENABLED', false),
        'model' => env('XERO_TENANCY_MODEL'),
        'current_method' => env('XERO_TENANCY_CURRENT_METHOD'),
        'session_name' => env('XERO_TENANCY_SESSION_NAME', 'xero_current_tenant_id'),
    ],

    'oauth' => [
        'client_id' => env('XERO_CLIENT_ID'),
        'client_secret' => env('XERO_CLIENT_SECRET'),
        'scopes' => env('XERO_SCOPES', implode(',', [
            'openid',
            'profile',
            'email',
            'offline_access',
            'accounting.settings',
            'accounting.banktransactions',
            'accounting.payments',
            'accounting.invoices',
            'accounting.manualjournals',
            'accounting.attachments',
            'accounting.contacts',
            'payroll.employees',
            'payroll.payruns',
            'payroll.timesheets',
            'payroll.settings',
        ])),
    ],

    'routes' => [
        'controllers' => [
            'index' => env('XERO_ROUTE_CONTROLLER_INDEX'),
            'auth' => env('XERO_ROUTE_CONTROLLER_AUTH', XeroAuthController::class),
            'callback' => env('XERO_ROUTE_CONTROLLER_CALLBACK', XeroCallbackController::class),
        ],
        'path' => env('XERO_ROUTE_PATH', 'xero'),
        'middleware' => env('XERO_ROUTE_MIDDLEWARE', ['web']),
        'exclude_middleware_for_callback' => env('XERO_ROUTE_EXCLUDE_MIDDLEWARE_FOR_CALLBACK', []),
        'callback_success_route' => env('XERO_ROUTE_CALLBACK_SUCCESS_ROUTE', 'xero.index'),
    ],
];
