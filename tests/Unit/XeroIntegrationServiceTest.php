<?php

namespace DcodeGroup\XeroIntegration\Tests\Unit;

use Calcinai\OAuth2\Client\Provider\Xero;
use DcodeGroup\XeroIntegration\Exceptions\UnauthorizedXero;
use DcodeGroup\XeroIntegration\Models\XeroToken;
use DcodeGroup\XeroIntegration\XeroIntegrationService;
use League\OAuth2\Client\Token\AccessToken;
use Mockery\MockInterface;

test('can get token model', function () {
    $token = XeroToken::factory()->create();

    $service = new XeroIntegrationService;
    $tokenModel = $service->getTokenModel();

    expect($tokenModel)->toBeInstanceOf(XeroToken::class)
        ->and($tokenModel->id)->toBe($token->id);
});

test('returns null when no token model exists', function () {
    $service = new XeroIntegrationService;
    $tokenModel = $service->getTokenModel();

    expect($tokenModel)->toBeNull();
});

test('can convert token model to oauth token', function () {
    $token = XeroToken::factory()->create();

    $service = new XeroIntegrationService;
    $oauthToken = $service->getToken($token);

    expect($oauthToken)->toBeInstanceOf(AccessToken::class)
        ->and($oauthToken->getToken())->toBe($token->access_token);
});

test('can get latest oauth token when no token passed', function () {
    XeroToken::factory()->create();

    $service = new XeroIntegrationService;
    $oauthToken = $service->getToken();

    expect($oauthToken)->toBeInstanceOf(AccessToken::class);
});

test('returns null when no token exists', function () {
    $service = new XeroIntegrationService;
    $oauthToken = $service->getToken();

    expect($oauthToken)->toBeNull();
});

test('can handle expired token and refresh it', function () {
    $expiredToken = XeroToken::factory()->create([
        'expires' => now()->subHours(1)->timestamp,
    ]);

    $service = new XeroIntegrationService;

    $this->mock(Xero::class, function (MockInterface $mock) use ($expiredToken) {
        $mock->shouldReceive('getAccessToken')
            ->with('refresh_token', ['refresh_token' => $expiredToken->refresh_token])
            ->andReturn(new AccessToken([
                'access_token' => 'new_access_token',
                'refresh_token' => 'new_refresh_token',
                'id_token' => 'new_id_token',
                'token_type' => 'Bearer',
                'expires' => now()->addHours(1)->timestamp,
                'scope' => 'openid email profile offline_access',
            ]));
    });

    try {
        $oauthToken = $service->getToken($expiredToken);
        expect($oauthToken)->toBeInstanceOf(AccessToken::class);
    } catch (UnauthorizedXero $e) {
        expect($e->getMessage())->toContain('invalid');
    }
});

test('throws exception when token format is invalid', function () {
    $expiredToken = XeroToken::factory()->create([
        'expires' => now()->subHours(1)->timestamp,
    ]);

    $service = new XeroIntegrationService;

    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAccessToken')
            ->andReturn(new AccessToken(['access_token' => 'invalid']));
    });

    expect(fn () => $service->getToken($expiredToken))
        ->toThrow(UnauthorizedXero::class);
});

test('can get auth url', function () {
    $service = new XeroIntegrationService;

    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAuthorizationUrl')
            ->andReturn('https://login.xero.com/identity/connect/authorize?client_id=test');
    });

    $url = $service->getAuthUrl();

    expect($url)->toBeString();
});

test('can save access token from code', function () {
    $service = new XeroIntegrationService;

    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAccessToken')
            ->with('authorization_code', ['code' => 'test_code'])
            ->andReturn(new AccessToken([
                'access_token' => 'new_access_token',
                'refresh_token' => 'new_refresh_token',
                'id_token' => 'new_id_token',
                'token_type' => 'Bearer',
                'expires' => now()->addHours(1)->timestamp,
                'scope' => 'openid email profile offline_access',
            ]));
    });

    $token = $service->saveAccessTokenFromCode('test_code');

    expect($token)->toBeInstanceOf(XeroToken::class)
        ->and($token->access_token)->toBe('new_access_token');
});

test('can change xero tenant', function () {
    $token = XeroToken::factory()->create();

    $service = new XeroIntegrationService;
    $updatedToken = $service->changeXeroTenant('new-tenant-id');

    expect($updatedToken)->toBeInstanceOf(XeroToken::class)
        ->and($updatedToken->current_tenant_id)->toBe('new-tenant-id');
});
