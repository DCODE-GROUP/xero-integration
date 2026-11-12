<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroEarningsRateData;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroPayrollCalendarData;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\ListXeroRecordsRequest;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Laravel\Facades\Saloon;

beforeEach(function () {
    Cache::flush();

    config(['data' => config('data') ?? require __DIR__.'/../../../vendor/spatie/laravel-data/config/data.php']);

    $this->xeroCalls = 0;
    $this->tenantsCalled = [];

    $this->mockXero = function (): void {
        Saloon::fake([
            ListXeroRecordsRequest::class => function (PendingRequest $request) {
                $this->xeroCalls++;
                $this->tenantsCalled[] = $request->headers()->get('Xero-tenant-id');

                return str_ends_with($request->getUri()->getPath(), '/PayItems')
                    ? MockResponse::make(['PayItems' => ['EarningsRates' => [['EarningsRateID' => 'rate-uuid', 'Name' => 'Ordinary Hours']]]])
                    : MockResponse::make(['PayrollCalendars' => [['PayrollCalendarID' => 'calendar-uuid', 'Name' => 'Weekly', 'CalendarType' => 'WEEKLY', 'StartDate' => '2026-11-02T00:00:00']]]);
            },
        ]);
    };
});

function connectTenant(string $tenantId): XeroToken
{
    return XeroToken::factory()->create(['current_tenant_id' => $tenantId]);
}

test('earning rates are cached after the first read', function () {
    connectTenant('tenant-a');
    ($this->mockXero)();

    $first = XeroEarningsRateData::cached();
    $second = XeroEarningsRateData::cached();

    expect($first->pluck('EarningsRateID')->all())->toBe(['rate-uuid'])
        ->and($second->pluck('EarningsRateID')->all())->toBe(['rate-uuid'])
        ->and($this->xeroCalls)->toBe(1);
});

test('each fetch is made for the tenant the cache key is built from', function () {
    $token = connectTenant('tenant-a');
    ($this->mockXero)();

    XeroEarningsRateData::cached();
    $token->update(['current_tenant_id' => 'tenant-b']);
    XeroEarningsRateData::cached();
    XeroPayrollCalendarData::cached();

    expect($this->tenantsCalled)->toBe(['tenant-a', 'tenant-b', 'tenant-b']);
});

test('the cache is isolated per tenant', function () {
    $token = connectTenant('tenant-a');
    ($this->mockXero)();

    XeroEarningsRateData::cached();
    $token->update(['current_tenant_id' => 'tenant-b']);
    XeroEarningsRateData::cached();

    expect($this->xeroCalls)->toBe(2)
        ->and(Cache::has('xero-integration:lookup:tenant-a:xero-earnings-rate-data'))->toBeTrue()
        ->and(Cache::has('xero-integration:lookup:tenant-b:xero-earnings-rate-data'))->toBeTrue();
});

test('forgetCached clears only the current tenant and type', function () {
    $token = connectTenant('tenant-a');
    ($this->mockXero)();

    XeroEarningsRateData::cached();
    $token->update(['current_tenant_id' => 'tenant-b']);
    XeroEarningsRateData::cached();

    XeroEarningsRateData::forgetCached();

    expect(Cache::has('xero-integration:lookup:tenant-b:xero-earnings-rate-data'))->toBeFalse()
        ->and(Cache::has('xero-integration:lookup:tenant-a:xero-earnings-rate-data'))->toBeTrue();

    XeroEarningsRateData::cached();

    expect($this->xeroCalls)->toBe(3);
});

test('a disconnected tenant returns empty collections without calling Xero', function () {
    ($this->mockXero)();

    expect(XeroEarningsRateData::cached())->toBeEmpty()
        ->and(XeroPayrollCalendarData::cached())->toBeEmpty()
        ->and(XeroPayrollCalendarData::findCached('calendar-uuid'))->toBeNull()
        ->and($this->xeroCalls)->toBe(0);

    XeroEarningsRateData::forgetCached();
});

test('a tenant without a selected organisation is treated as disconnected', function () {
    XeroToken::factory()->create(['current_tenant_id' => null]);
    ($this->mockXero)();

    expect(XeroEarningsRateData::cached())->toBeEmpty()
        ->and($this->xeroCalls)->toBe(0);
});

test('the global cache ttl comes from config and is applied when caching', function () {
    connectTenant('tenant-a');
    ($this->mockXero)();
    config([
        'xero-integration.cache.ttl' => 120,
        'xero-integration.cache.types' => [],
    ]);

    XeroEarningsRateData::cached();

    expect(XeroEarningsRateData::lookupCacheTtl())->toBe(120)
        ->and(Cache::has('xero-integration:lookup:tenant-a:xero-earnings-rate-data'))->toBeTrue();

    $this->travel(119)->seconds();
    expect(Cache::has('xero-integration:lookup:tenant-a:xero-earnings-rate-data'))->toBeTrue();

    $this->travel(2)->seconds();
    expect(Cache::has('xero-integration:lookup:tenant-a:xero-earnings-rate-data'))->toBeFalse();
});

test('a per-type ttl overrides the global ttl only for that type', function () {
    connectTenant('tenant-a');
    ($this->mockXero)();
    config([
        'xero-integration.cache.ttl' => 120,
        'xero-integration.cache.types' => [XeroEarningsRateData::class => ['ttl' => 30]],
    ]);

    expect(XeroEarningsRateData::lookupCacheTtl())->toBe(30)
        ->and(XeroPayrollCalendarData::lookupCacheTtl())->toBe(120);
});

test('caching can be disabled globally', function () {
    connectTenant('tenant-a');
    ($this->mockXero)();
    config(['xero-integration.cache.enabled' => false, 'xero-integration.cache.types' => []]);

    XeroEarningsRateData::cached();
    XeroEarningsRateData::cached();

    expect($this->xeroCalls)->toBe(2);
});

test('caching can be disabled or enabled for a single type', function () {
    connectTenant('tenant-a');
    ($this->mockXero)();
    config([
        'xero-integration.cache.enabled' => true,
        'xero-integration.cache.types' => [XeroEarningsRateData::class => ['enabled' => false]],
    ]);

    XeroEarningsRateData::cached();
    XeroEarningsRateData::cached();
    XeroPayrollCalendarData::cached();
    XeroPayrollCalendarData::cached();

    expect($this->xeroCalls)->toBe(3);

    config([
        'xero-integration.cache.enabled' => false,
        'xero-integration.cache.types' => [XeroEarningsRateData::class => ['enabled' => true]],
    ]);

    expect(XeroEarningsRateData::lookupCacheEnabled())->toBeTrue()
        ->and(XeroPayrollCalendarData::lookupCacheEnabled())->toBeFalse();
});

test('payroll calendars are cached and can be found by id', function () {
    connectTenant('tenant-a');
    ($this->mockXero)();

    expect(XeroPayrollCalendarData::findCached('calendar-uuid')?->Name)->toBe('Weekly')
        ->and(XeroPayrollCalendarData::findCached('missing'))->toBeNull()
        ->and($this->xeroCalls)->toBe(1);
});

test('a result fetched while the tenant changed is not cached', function () {
    $token = connectTenant('tenant-a');
    Saloon::fake([
        ListXeroRecordsRequest::class => function () use ($token) {
            $token->update(['current_tenant_id' => 'tenant-b']);

            return MockResponse::make(['PayItems' => ['EarningsRates' => [['EarningsRateID' => 'rate-uuid', 'Name' => 'Ordinary Hours']]]]);
        },
    ]);

    XeroEarningsRateData::cached();

    expect(Cache::has('xero-integration:lookup:tenant-a:xero-earnings-rate-data'))->toBeFalse()
        ->and(Cache::has('xero-integration:lookup:tenant-b:xero-earnings-rate-data'))->toBeFalse();
});
