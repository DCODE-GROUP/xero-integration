<?php

namespace DcodeGroup\XeroIntegration\Tests\Unit;

use DcodeGroup\XeroIntegration\Models\XeroToken;
use DcodeGroup\XeroIntegration\XeroApp;
use DcodeGroup\XeroIntegration\XeroIntegration;

test('xero integration returns self when calling query builder methods', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;
    $integration = $app->invoices();

    expect($integration)->toBeInstanceOf(XeroIntegration::class)
        ->and($integration->limit(10))->toBeInstanceOf(XeroIntegration::class);
});

test('xero integration limit calls query pageSize', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;
    $integration = $app->invoices();

    $result = $integration->limit(25);

    expect($result)->toBeInstanceOf(XeroIntegration::class);
});

test('xero integration returns instance for different relationship types', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;

    $invoices = $app->invoices();
    $contacts = $app->contacts();
    $quotes = $app->quotes();

    expect($invoices)->toBeInstanceOf(XeroIntegration::class)
        ->and($contacts)->toBeInstanceOf(XeroIntegration::class)
        ->and($quotes)->toBeInstanceOf(XeroIntegration::class);
});

test('xero integration methods can be chained in sequence', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;

    $result = $app->contacts()->limit(100);

    expect($result)->toBeInstanceOf(XeroIntegration::class);
});
