<?php

namespace DcodeGroup\XeroIntegration\Tests\Feature;

use Calcinai\OAuth2\Client\Provider\Xero;
use DcodeGroup\XeroIntegration\Http\Controllers\XeroAuthController;
use DcodeGroup\XeroIntegration\Http\Controllers\XeroCallbackController;
use DcodeGroup\XeroIntegration\Models\XeroToken;
use Illuminate\Http\Response;
use League\OAuth2\Client\Token\AccessToken;
use Mockery\MockInterface;

test('auth controller redirects to xero login', function () {
    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAuthorizationUrl')
            ->andReturn('https://login.xero.com/identity/connect/authorize?client_id=test');
    });

    $controller = new XeroAuthController();
    $response = $controller();

    expect($response->status())->toBe(Response::HTTP_FOUND);
});

test('callback controller validates required code parameter', function () {
    $controller = new XeroCallbackController();
    $request = \Illuminate\Http\Request::create('/?code=', 'GET');

    expect(fn () => $controller($request))
        ->toThrow(\DcodeGroup\XeroIntegration\Exceptions\UnauthorizedXero::class);
});

test('callback controller saves token from authorization code', function () {
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

    config()->set('xero-integration.routes.callback_success_route', '/dashboard');

    $controller = new XeroCallbackController();
    $request = \Illuminate\Http\Request::create('/?code=auth_code_123', 'GET');

    $response = $controller($request);

    expect($response->status())->toBe(Response::HTTP_FOUND)
        ->and(XeroToken::count())->toBe(1);
});

test('callback creates new token record', function () {
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

    config()->set('xero-integration.routes.callback_success_route', '/dashboard');

    expect(XeroToken::count())->toBe(0);

    $controller = new XeroCallbackController();
    $request = \Illuminate\Http\Request::create('/?code=test_code', 'GET');
    $controller($request);

    expect(XeroToken::count())->toBe(1);
    $token = XeroToken::first();
    expect($token->access_token)->toBe('token_123');
});

test('callback redirects to success route', function () {
    config()->set('xero-integration.routes.callback_success_route', '/xero/dashboard');

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

    $controller = new XeroCallbackController();
    $request = \Illuminate\Http\Request::create('/?code=test_code', 'GET');
    $response = $controller($request);

    expect($response->status())->toBe(Response::HTTP_FOUND)
        ->and($response->getTargetUrl())->toContain('/xero/dashboard');
});

test('auth controller can be invoked directly', function () {
    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAuthorizationUrl')
            ->andReturn('https://login.xero.com/test');
    });

    $controller = new XeroAuthController();
    $response = $controller();

    expect($response->status())->toBe(Response::HTTP_FOUND);
});

test('callback controller handles valid request', function () {
    config()->set('xero-integration.routes.callback_success_route', '/success');

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

    $controller = new XeroCallbackController();
    $request = \Illuminate\Http\Request::create('/?code=valid_code', 'GET');

    $response = $controller($request);

    expect($response->status())->toBe(Response::HTTP_FOUND);
});
