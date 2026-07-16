<?php

namespace DcodeGroup\XeroIntegration\Tests\Unit\Models;

use DcodeGroup\XeroIntegration\Models\XeroToken;
use League\OAuth2\Client\Token\AccessToken;

test('can create xero token', function () {
    $token = XeroToken::factory()->create();

    expect($token)->toBeInstanceOf(XeroToken::class)
        ->and($token->id_token)->not->toBeNull()
        ->and($token->access_token)->not->toBeNull();
});

test('can retrieve latest token', function () {
    XeroToken::factory(3)->create();
    $latest = XeroToken::latestToken();

    expect($latest)->toBeInstanceOf(XeroToken::class)
        ->and($latest->id)->toBe(3);
});

test('can convert xero token to oauth2 token', function () {
    $token = XeroToken::factory()->create();
    $oauth2Token = $token->toOAuth2Token();

    expect($oauth2Token)->toBeInstanceOf(AccessToken::class)
        ->and($oauth2Token->getToken())->toBe($token->access_token)
        ->and($oauth2Token->getRefreshToken())->toBe($token->refresh_token);
});

test('can validate token format', function () {
    $token = XeroToken::factory()->create();
    $oauth2Token = $token->toOAuth2Token();

    expect(XeroToken::isValidTokenFormat($oauth2Token))->toBeTrue();
});

test('can update token attributes', function () {
    $token = XeroToken::factory()->create(['access_token' => 'old_token']);
    $token->update(['access_token' => 'new_token']);

    expect($token->refresh()->access_token)->toBe('new_token');
});

test('can delete token', function () {
    $token = XeroToken::factory()->create();
    $tokenId = $token->id;
    $token->delete();

    expect(XeroToken::find($tokenId))->toBeNull();
});

test('can validate invalid token format', function () {
    $invalidToken = new AccessToken(['access_token' => 'token', 'token_type' => 'Bearer']);

    expect(XeroToken::isValidTokenFormat($invalidToken))->toBeFalse();
});

test('token timestamp is set correctly', function () {
    $token = XeroToken::factory()->create();

    expect($token->created_at)->not->toBeNull()
        ->and($token->updated_at)->not->toBeNull();
});

test('can retrieve token with all fields', function () {
    $token = XeroToken::factory()->create([
        'current_tenant_id' => 'test-tenant-123',
        'expires' => 1234567890,
    ]);

    $retrieved = XeroToken::find($token->id);

    expect($retrieved->current_tenant_id)->toBe('test-tenant-123')
        ->and($retrieved->expires)->toBe(1234567890);
});
