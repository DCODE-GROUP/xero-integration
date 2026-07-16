<?php

namespace DcodeGroup\XeroIntegration\Tests\Tenancy;

use DcodeGroup\XeroIntegration\Models\XeroToken;
use Workbench\App\Models\Tenant;

beforeEach(function () {
    // Disable tenancy for these tests
    config()->set('xero-integration.tenancy.enabled', false);
});

test('when tenancy is disabled, all tokens are accessible', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);
    $token3 = XeroToken::factory()->create(['tenant_id' => null]);

    // Without tenancy, all tokens should be accessible
    $tokens = XeroToken::all();
    expect($tokens)->toHaveCount(3);
});

test('when tenancy is disabled, session tenant id is ignored', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    // Set a session tenant id
    session()->put('xero_current_tenant_id', $tenant1->id);

    // Without tenancy, both tokens should be accessible
    $tokens = XeroToken::all();
    expect($tokens)->toHaveCount(2);

    // Switch team
    session()->put('xero_current_tenant_id', $tenant2->id);

    // Still both tokens should be accessible
    $tokens = XeroToken::all();
    expect($tokens)->toHaveCount(2);
});

test('latestToken returns most recent token when tenancy is disabled', function () {
    $tenant1 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    sleep(1);
    $token2 = XeroToken::factory()->create(['tenant_id' => null]);

    $latest = XeroToken::latestToken();

    expect($latest->id)->toBe($token2->id);
});
