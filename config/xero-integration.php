<?php

// config for DcodeGroup/XeroIntegration
return [
    'tenancy' => [
        'enabled' => env('XERO_TENANCY_ENABLED', false),
        'model' => null,
        'current_method' => null,
        'session_name' => 'xero_current_tenant_id',
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
];
