<?php

namespace DcodeGroup\XeroIntegration\Tests\Unit;

use DcodeGroup\XeroIntegration\Exceptions\XeroIntegrationException;
use DcodeGroup\XeroIntegration\Models\XeroToken;
use DcodeGroup\XeroIntegration\XeroApp;
use DcodeGroup\XeroIntegration\XeroIntegration;
use DcodeGroup\XeroIntegration\XeroQuery;

test('can load relationship model', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;

    expect($app->getModelForRelationship('invoices'))
        ->toBe('XeroPHP\Models\Accounting\Invoice');
});

test('can throw exception for invalid relationship', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;

    expect(fn () => $app->getModelForRelationship('invalid_model'))
        ->toThrow(XeroIntegrationException::class);
});

test('relationship to model map contains required models', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;

    expect($app->getModelForRelationship('contacts'))
        ->toBe('XeroPHP\Models\Accounting\Contact');

    expect($app->getModelForRelationship('quotes'))
        ->toBe('XeroPHP\Models\Accounting\Quote');

    expect($app->getModelForRelationship('invoices'))
        ->toBe('XeroPHP\Models\Accounting\Invoice');
});

test('can call method on app via call method', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;

    $result = $app->invoices();

    expect($result)->toBeInstanceOf(XeroIntegration::class);
});

test('can throw exception when no token found', function () {
    expect(fn () => new XeroApp)
        ->toThrow(XeroIntegrationException::class, 'No Xero token found');
});

test('can get available relationships', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;

    $reflection = new \ReflectionMethod($app, 'getAvailableRelationships');
    $reflection->setAccessible(true);
    $relationships = $reflection->invoke($app);

    expect($relationships)->toHaveCount(3)
        ->and($relationships->toArray())->toEqual(['contacts', 'invoices', 'quotes']);
});

test('__call throws exception for invalid relationship', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;

    expect(fn () => $app->invalid_model())
        ->toThrow(XeroIntegrationException::class, "Model 'invalid_model' not found in Xero integration.");
});

test('__get returns null for invalid relationship', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;

    expect($app->invalid_relation)->toBeNull();
});

test('load returns XeroQuery instance', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;

    $result = $app->load('XeroPHP\Models\Accounting\Invoice');

    expect($result)->toBeInstanceOf(XeroQuery::class);
});
