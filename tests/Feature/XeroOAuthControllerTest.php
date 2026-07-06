<?php

namespace DcodeGroup\XeroIntegration\Tests\Feature;

use Calcinai\OAuth2\Client\Provider\Xero;
use DcodeGroup\XeroIntegration\Exceptions\UnauthorizedXero;
use DcodeGroup\XeroIntegration\Models\XeroToken;
use League\OAuth2\Client\Token\AccessToken;
use Mockery\MockInterface;

beforeEach(function () {
    config()->set('xero-integration.routes.callback_success_route', 'dashboard');
});

test('auth route redirects to xero for authorization', function () {
    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAuthorizationUrl')
            ->andReturn('https://login.xero.com/identity/connect/authorize?client_id=test');
    });

    $this->get('/xero/auth')
        ->assertRedirect('https://login.xero.com/identity/connect/authorize?client_id=test');
});

test('callback route throws unauthorized exception when code is missing', function () {
    $this->withoutExceptionHandling();

    $this->get('/xero/callback');
})->throws(UnauthorizedXero::class, 'Could not authorize Xero!');

test('callback route saves token from authorization code and redirects', function () {
    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAccessToken')
            ->with('authorization_code', ['code' => 'auth_code_123'])
            ->andReturn(new AccessToken([
                'access_token' => 'new_access_token',
                'refresh_token' => 'new_refresh_token',
                'id_token' => 'new_id_token',
                'token_type' => 'Bearer',
                'expires' => now()->addHours(1)->timestamp,
                'scope' => 'openid email profile offline_access',
            ]));
    });

    expect(XeroToken::count())->toBe(0);

    $this->get('/xero/callback?code=auth_code_123')
        ->assertRedirect('dashboard');

    expect(XeroToken::count())->toBe(1);
    $token = XeroToken::first();
    expect($token->access_token)->toBe('new_access_token');
    expect($token->refresh_token)->toBe('new_refresh_token');
});

test('callback route creates new token record with correct attributes', function () {
    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAccessToken')
            ->andReturn(new AccessToken([
                'access_token' => 'token_123',
                'refresh_token' => 'refresh_123',
                'id_token' => 'id_token_123',
                'token_type' => 'Bearer',
                'expires' => now()->addHours(1)->timestamp,
                'scope' => 'openid email profile offline_access',
            ]));
    });

    expect(XeroToken::count())->toBe(0);

    $this->get('/xero/callback?code=test_code');

    expect(XeroToken::count())->toBe(1);

    $token = XeroToken::first();
    expect($token->access_token)->toBe('token_123');
    expect($token->refresh_token)->toBe('refresh_123');
    expect($token->id_token)->toBe('id_token_123');
});

test('callback route redirects to configured success route', function () {
    config()->set('xero-integration.routes.callback_success_route', 'xero/dashboard');

    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAccessToken')
            ->andReturn(new AccessToken([
                'access_token' => 'token',
                'refresh_token' => 'refresh',
                'id_token' => 'id',
                'token_type' => 'Bearer',
                'expires' => now()->addHours(1)->timestamp,
                'scope' => 'openid email profile offline_access',
            ]));
    });

    $this->get('/xero/callback?code=test_code')
        ->assertRedirect('xero/dashboard');
});

test('auth route returns redirect response', function () {
    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAuthorizationUrl')
            ->andReturn('https://login.xero.com/test');
    });

    $this->get('/xero/auth')
        ->assertStatus(302);
});

test('callback route handles valid request', function () {
    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAccessToken')
            ->andReturn(new AccessToken([
                'access_token' => 'valid_token',
                'refresh_token' => 'valid_refresh',
                'id_token' => 'valid_id',
                'token_type' => 'Bearer',
                'expires' => now()->addHours(1)->timestamp,
                'scope' => 'openid',
            ]));
    });

    $this->get('/xero/callback?code=valid_code')
        ->assertStatus(302);
});
