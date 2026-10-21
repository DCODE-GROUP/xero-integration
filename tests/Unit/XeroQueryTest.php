<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit;

use Calcinai\OAuth2\Client\Provider\Xero;
use Dcodegroup\XeroIntegration\Exceptions\XeroRateLimitExceededException;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Dcodegroup\XeroIntegration\XeroApp;
use Dcodegroup\XeroIntegration\XeroQuery;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\RateLimiter;
use League\OAuth2\Client\Token\AccessToken;
use Mockery\MockInterface;
use Workbench\App\Services\TenantResolverService;

class RefreshableTestXeroApp extends XeroApp
{
    public static ?MockHandler $handler = null;

    public function refreshAfterUnauthorized(): void
    {
        parent::refreshAfterUnauthorized();
        $this->setTransport(new Client(['handler' => self::$handler]));
    }
}

test('rate limiter key generated without tenancy', function () {
    config(['xero-integration.tenancy.enabled' => false]);

    $key = XeroQuery::getRateLimiterKey();

    expect($key)->toContain('XeroQuery')
        ->and($key)->not->toContain('tenant');
});

test('rate limiter key includes tenant when tenancy enabled', function () {
    config(['xero-integration.tenancy.enabled' => true]);
    config(['xero-integration.tenancy.tenant_resolver' => [TenantResolverService::class, 'getTenant']]);

    $key = XeroQuery::getRateLimiterKey();

    expect($key)->toBe('Dcodegroup\XeroIntegration\XeroQuery:1');
});

test('xero query can be created from xero app', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;
    $query = $app->load('XeroPHP\Models\Accounting\Invoice');

    expect($query)->toBeInstanceOf(XeroQuery::class);
});

test('xero query has correct configuration', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;
    $query = $app->load('XeroPHP\Models\Accounting\Invoice');

    expect($query)->toBeInstanceOf(XeroQuery::class);
});

test('rate limiter throws exception when limit exceeded', function () {
    XeroToken::factory()->create();

    $app = new XeroApp;
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

test('retries a recoverable xero 401 after refreshing the connection token', function () {
    $token = XeroToken::factory()->create();

    $this->mock(Xero::class, function (MockInterface $mock) use ($token) {
        $mock->shouldReceive('getAccessToken')
            ->once()
            ->with('refresh_token', ['refresh_token' => $token->refresh_token])
            ->andReturn(new AccessToken([
                'access_token' => 'rotated_access_token',
                'refresh_token' => 'rotated_refresh_token',
                'id_token' => 'rotated_id_token',
                'token_type' => 'Bearer',
                'expires' => now()->addHours(1)->timestamp,
                'scope' => 'openid email profile offline_access',
            ]));
    });

    $handler = new MockHandler([
        new Response(401, ['Content-Type' => 'text/html'], 'oauth_problem_advice=token_expired'),
        new Response(200, [], '<Response><Invoices /></Response>'),
    ]);
    RefreshableTestXeroApp::$handler = $handler;
    $app = new RefreshableTestXeroApp;
    $app->setTransport(new Client(['handler' => $handler]));

    $results = $app->load('XeroPHP\\Models\\Accounting\\Invoice')->execute();

    expect($results)->toBeEmpty()
        ->and($token->fresh()->access_token)->toBe('rotated_access_token')
        ->and($token->fresh()->refresh_token)->toBe('rotated_refresh_token');
});
