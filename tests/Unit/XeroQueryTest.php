<?php

namespace DcodeGroup\XeroIntegration\Tests\Unit;

use DcodeGroup\XeroIntegration\Exceptions\XeroRateLimitExceededException;
use DcodeGroup\XeroIntegration\Models\XeroToken;
use DcodeGroup\XeroIntegration\XeroApp;
use DcodeGroup\XeroIntegration\XeroQuery;
use Illuminate\Support\Facades\RateLimiter;

test('rate limiter key generated without tenancy', function () {
    config()->set('xero-integration.tenancy.enabled', false);

    $key = XeroQuery::getRateLimiterKey();

    expect($key)->toContain('XeroQuery')
        ->and($key)->not->toContain('tenant');
});

test('rate limiter key includes tenant when tenancy enabled', function () {
    config()->set('xero-integration.tenancy.enabled', true);
    config()->set('xero-integration.tenancy.current_method', function () {
        return (object) ['getKey' => fn () => 'test-tenant-id'];
    });
    
    session()->put('xero_current_tenant_id', 'test-tenant-id');

    $method = config('xero-integration.tenancy.current_method');
    expect($method())->not->toBeNull();
});

test('xero query can be created from xero app', function () {
    XeroToken::factory()->create();

    $app = new XeroApp();
    $query = $app->load('XeroPHP\Models\Accounting\Invoice');

    expect($query)->toBeInstanceOf(XeroQuery::class);
});

test('xero query has correct configuration', function () {
    XeroToken::factory()->create();

    $app = new XeroApp();
    $query = $app->load('XeroPHP\Models\Accounting\Invoice');

    expect($query)->toBeInstanceOf(XeroQuery::class);
});

test('rate limiter throws exception when limit exceeded', function () {
    XeroToken::factory()->create();

    $app = new XeroApp();
    $query = $app->load('XeroPHP\Models\Accounting\Invoice');

    $key = XeroQuery::getRateLimiterKey();
    RateLimiter::resetAttempts($key);

    for ($i = 0; $i < 60; $i++) {
        RateLimiter::hit($key);
    }

    expect(fn () => $query->execute())
        ->toThrow(XeroRateLimitExceededException::class, 'rate limit exceeded');
});

test('can reset rate limiter', function () {
    $key = XeroQuery::getRateLimiterKey();
    
    RateLimiter::hit($key);
    RateLimiter::hit($key);
    
    $beforeReset = RateLimiter::remaining($key, 60);
    RateLimiter::resetAttempts($key);
    $afterReset = RateLimiter::remaining($key, 60);

    expect($afterReset)->toBeGreaterThan($beforeReset);
});

test('rate limiter respects configured limit', function () {
    config()->set('services.xero.rate_limit.no', 60);
    
    $key = XeroQuery::getRateLimiterKey();
    RateLimiter::resetAttempts($key);

    $remaining = RateLimiter::remaining($key, 60);

    expect($remaining)->toEqual(60);
});

test('multiple queries share same rate limiter key', function () {
    XeroToken::factory()->create();

    config()->set('xero-integration.tenancy.enabled', false);

    $key1 = XeroQuery::getRateLimiterKey();
    $key2 = XeroQuery::getRateLimiterKey();

    expect($key1)->toEqual($key2);
});

test('rate limit decay uses configured seconds', function () {
    config()->set('services.xero.rate_limit.decay_seconds', 60);

    $key = XeroQuery::getRateLimiterKey();
    RateLimiter::resetAttempts($key);

    RateLimiter::hit($key);
    
    $remaining = RateLimiter::remaining($key, 60);

    expect($remaining)->toBeLessThan(60)
        ->and($remaining)->toBeGreaterThan(0);
});
