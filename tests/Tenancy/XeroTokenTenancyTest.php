<?php

namespace DcodeGroup\XeroIntegration\Tests\Tenancy;

use DcodeGroup\XeroIntegration\Models\XeroToken;
use Workbench\App\Models\Tenant;

beforeEach(function () {
    // Enable tenancy for these tests
    config()->set('xero-integration.tenancy.enabled', true);
    config()->set('xero-integration.tenancy.session_name', 'xero_current_tenant_id');
});

afterEach(function () {
    session()->forget('xero_current_tenant_id');
});

test('tokens are scoped to the current tenant when tenancy is enabled', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    // Create a token for tenant 1
    session()->put('xero_current_tenant_id', $tenant1->id);
    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);

    // Create a token for tenant 2
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    // Query tokens while in tenant 1 context
    session()->put('xero_current_tenant_id', $tenant1->id);
    $tokens = XeroToken::all();

    expect($tokens)->toHaveCount(1)
        ->and($tokens->first()->id)->toBe($token1->id)
        ->and($tokens->first()->tenant_id)->toBe($tenant1->id);
});

test('tokens for different tenants are not accessible from other tenant contexts', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    // Create tokens for each tenant
    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    // Query from tenant 1 context
    session()->put('xero_current_tenant_id', $tenant1->id);
    $tenant1Tokens = XeroToken::all();
    expect($tenant1Tokens)->toHaveCount(1)
        ->and($tenant1Tokens->first()->id)->toBe($token1->id);

    // Query from tenant 2 context
    session()->put('xero_current_tenant_id', $tenant2->id);
    $tenant2Tokens = XeroToken::all();
    expect($tenant2Tokens)->toHaveCount(1)
        ->and($tenant2Tokens->first()->id)->toBe($token2->id);
});

test('no token leakage between tenants - tenant1 cannot see tenant2 tokens', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    session()->put('xero_current_tenant_id', $tenant1->id);
    $retrievedTokens = XeroToken::all();

    // Tenant 1 should not see tenant 2's token
    expect($retrievedTokens->pluck('id')->toArray())->not()->toContain($token2->id)
        ->and($retrievedTokens->pluck('id')->toArray())->toContain($token1->id);
});

test('no token leakage between tenants - tenant2 cannot see tenant1 tokens', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    session()->put('xero_current_tenant_id', $tenant2->id);
    $retrievedTokens = XeroToken::all();

    // Tenant 2 should not see tenant 1's token
    expect($retrievedTokens->pluck('id')->toArray())->not()->toContain($token1->id)
        ->and($retrievedTokens->pluck('id')->toArray())->toContain($token2->id);
});

test('multiple tokens for the same tenant are all scoped correctly', function () {
    $tenant1 = Tenant::factory()->create();

    // Create multiple tokens for tenant 1
    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token3 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);

    session()->put('xero_current_tenant_id', $tenant1->id);
    $tokens = XeroToken::all();

    expect($tokens)->toHaveCount(3)
        ->and($tokens->pluck('id')->toArray())->toContain($token1->id, $token2->id, $token3->id);
});

test('switching between tenants changes the visible tokens', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    // Start with tenant 1
    session()->put('xero_current_tenant_id', $tenant1->id);
    expect(XeroToken::all())->toHaveCount(1)->and(XeroToken::first()->id)->toBe($token1->id);

    // Switch to tenant 2
    session()->put('xero_current_tenant_id', $tenant2->id);
    expect(XeroToken::all())->toHaveCount(1)->and(XeroToken::first()->id)->toBe($token2->id);

    // Switch back to tenant 1
    session()->put('xero_current_tenant_id', $tenant1->id);
    expect(XeroToken::all())->toHaveCount(1)->and(XeroToken::first()->id)->toBe($token1->id);
});

test('when session tenant id is not set, no tokens are retrieved', function () {
    $tenant1 = Tenant::factory()->create();

    XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    XeroToken::factory()->create(['tenant_id' => $tenant1->id]);

    // Clear the session
    session()->forget('xero_current_tenant_id');

    // Should return no tokens as tenant_id will be -1
    $tokens = XeroToken::all();
    expect($tokens)->toHaveCount(0);
});

test('can find a specific token for the current tenant', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    session()->put('xero_current_tenant_id', $tenant1->id);
    $foundToken = XeroToken::find($token1->id);

    expect($foundToken)->not()->toBeNull()
        ->and($foundToken->id)->toBe($token1->id)
        ->and($foundToken->tenant_id)->toBe($tenant1->id);
});

test('cannot find another tenant\'s token even if you know the id', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    session()->put('xero_current_tenant_id', $tenant1->id);

    // Try to find tenant 2's token while in tenant 1 context
    $foundToken = XeroToken::find($token2->id);

    expect($foundToken)->toBeNull();
});

test('token relationship works correctly with tenant scoping', function () {
    $tenant1 = Tenant::factory()->create(['name' => 'Tenant 1']);
    $tenant2 = Tenant::factory()->create(['name' => 'Tenant 2']);

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    session()->put('xero_current_tenant_id', $tenant1->id);

    $retrievedToken = XeroToken::find($token1->id);
    $tenant = $retrievedToken->tenant()->first();

    expect($tenant)->not()->toBeNull()
        ->and($tenant->name)->toBe('Tenant 1');
});

test('query builder methods respect tenant scoping', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    XeroToken::factory()->count(3)->create(['tenant_id' => $tenant1->id]);
    XeroToken::factory()->count(2)->create(['tenant_id' => $tenant2->id]);

    session()->put('xero_current_tenant_id', $tenant1->id);

    expect(XeroToken::count())->toBe(3)
        ->and(XeroToken::where('tenant_id', $tenant1->id)->count())->toBe(3)
        ->and(XeroToken::latest('id')->count())->toBe(3);
});

test('update operations respect tenant scoping', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id, 'access_token' => 'old_token']);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id, 'access_token' => 'old_token']);

    session()->put('xero_current_tenant_id', $tenant1->id);

    $token = XeroToken::first();
    $token->update(['access_token' => 'new_token']);

    // Verify the update only affected tenant 1's token
    $refreshedToken1 = XeroToken::withoutGlobalScopes()->find($token1->id);
    $refreshedToken2 = XeroToken::withoutGlobalScopes()->find($token2->id);

    expect($refreshedToken1->access_token)->toBe('new_token')
        ->and($refreshedToken2->access_token)->toBe('old_token');
});

test('delete operations respect tenant scoping', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    session()->put('xero_current_tenant_id', $tenant1->id);
    XeroToken::where('id', $token1->id)->delete();

    // Tenant 1's token should be deleted
    expect(XeroToken::withoutGlobalScopes()->find($token1->id))->toBeNull();

    // Tenant 2's token should still exist
    expect(XeroToken::withoutGlobalScopes()->find($token2->id))->not()->toBeNull();
});

test('withoutGlobalScopes bypasses tenant filtering', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    session()->put('xero_current_tenant_id', $tenant1->id);

    // With scope, only see tenant 1's token
    expect(XeroToken::all())->toHaveCount(1);

    // Without scope, see all tokens
    expect(XeroToken::withoutGlobalScopes()->get())->toHaveCount(2);
});

test('tenant field is properly populated on token creation', function () {
    $tenant1 = Tenant::factory()->create();

    session()->put('xero_current_tenant_id', $tenant1->id);

    $token = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);

    expect($token->tenant_id)->toBe($tenant1->id);
});

test('latestToken respects tenant scoping', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $token1 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);
    sleep(1); // Ensure different timestamps
    $token2 = XeroToken::factory()->create(['tenant_id' => $tenant1->id]);

    $token3 = XeroToken::factory()->create(['tenant_id' => $tenant2->id]);

    session()->put('xero_current_tenant_id', $tenant1->id);

    $latest = XeroToken::latestToken();

    expect($latest->id)->toBe($token2->id)
        ->and($latest->tenant_id)->toBe($tenant1->id);
});

