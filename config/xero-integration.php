<?php

use Dcodegroup\XeroIntegration\Http\Controllers\XeroAuthController;
use Dcodegroup\XeroIntegration\Http\Controllers\XeroCallbackController;

// config for DcodeGroup/XeroIntegration
return [
    'tenancy' => [
        'enabled' => env('XERO_TENANCY_ENABLED', false),
        'model' => env('XERO_TENANCY_MODEL'),
        'tenant_resolver' => env('XERO_TENANCY_TENANT_RESOLVER'),
        'slug_name' => env('XERO_TENANCY_SLUG'),
        'session_name' => env('XERO_TENANCY_SESSION_NAME', 'xero_current_tenant_id'),
    ],

    'oauth' => [
        'client_id' => env('XERO_CLIENT_ID'),
        'client_secret' => env('XERO_CLIENT_SECRET'),
        'scopes' => env('XERO_SCOPES', implode(' ', [
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
        'state' => env('XERO_STATE'),
    ],

    'routes' => [
        'controllers' => [
            'auth' => env('XERO_ROUTE_CONTROLLER_AUTH', XeroAuthController::class),
            'callback' => env('XERO_ROUTE_CONTROLLER_CALLBACK', XeroCallbackController::class),
        ],
        'path' => env('XERO_ROUTE_PATH', 'xero'),
        'middleware' => env('XERO_ROUTE_MIDDLEWARE', ['web']),
        'exclude_middleware_for_callback' => env('XERO_ROUTE_EXCLUDE_MIDDLEWARE_FOR_CALLBACK', []),
        'success_url_session_name' => env('XERO_ROUTE_SUCCESS_URL_SESSION_NAME', 'xero_success_url'),
    ],
    'webhooks' => [
        'secret' => env('XERO_WEBHOOK_SECRET'),
        'prefix' => env('XERO_WEBHOOK_PREFIX', 'webhooks'),
        'middleware' => env('XERO_WEBHOOK_MIDDLEWARE', ['guest']),
        'queue' => env('XERO_WEBHOOK_QUEUE', 'default'),
        'backoffs' => env('XERO_WEBHOOK_BACKOFFS', [10, 30, 60, 120, 300]),
    ],
    'rate_limit' => [
        'no' => env('XERO_RATE_LIMIT_NO', 60),
        'decay_seconds' => env('XERO_RATE_LIMIT_DECAY_SECONDS', 60),
    ],
];
