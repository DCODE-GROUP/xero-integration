<?php

namespace Dcodegroup\XeroIntegration\Data\Traits;

use Dcodegroup\XeroIntegration\Data\Contracts\ListsXeroRecords;
use Dcodegroup\XeroIntegration\Facades\XeroIntegrationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Per-tenant caching of a data type's `list()` for rarely-changing lookups, such as earnings rates.
 *
 * Enabled state and TTL come from `xero-integration.cache`, with optional per-type overrides under
 * `xero-integration.cache.types.<class>`.
 *
 * @phpstan-require-implements ListsXeroRecords
 */
trait CachesXeroLookups
{
    /**
     * The cached `list()` for the current tenant. A disconnected tenant returns an empty collection.
     *
     * @return Collection<int, self>
     */
    public static function cached(): Collection
    {
        $tenantId = static::lookupTenantId();

        if ($tenantId === null) {
            return collect();
        }

        if (! static::lookupCacheEnabled()) {
            return static::list();
        }

        $key = static::lookupCacheKey($tenantId);
        $cached = Cache::get($key);

        if ($cached instanceof Collection) {
            return $cached;
        }

        $records = static::list();

        // Only store the result when the tenant did not change while fetching.
        if (static::lookupTenantId() === $tenantId) {
            Cache::put($key, $records, static::lookupCacheTtl());
        }

        return $records;
    }

    /**
     * Whether caching is on for this type: the per-type setting, else the global one.
     */
    public static function lookupCacheEnabled(): bool
    {
        return (bool) (config('xero-integration.cache.types.'.static::class.'.enabled')
            ?? config('xero-integration.cache.enabled', true));
    }

    /**
     * Cache TTL in seconds for this type: the per-type setting, else the global one.
     */
    public static function lookupCacheTtl(): int
    {
        return (int) (config('xero-integration.cache.types.'.static::class.'.ttl')
            ?? config('xero-integration.cache.ttl', 3600));
    }

    /**
     * Clear the cached list for the current tenant, so the next `cached()` call hits Xero.
     */
    public static function forgetCached(): void
    {
        $tenantId = static::lookupTenantId();

        if ($tenantId !== null) {
            Cache::forget(static::lookupCacheKey($tenantId));
        }
    }

    /**
     * The connected Xero organisation, or null when the tenant is not connected.
     */
    protected static function lookupTenantId(): ?string
    {
        $tenantId = XeroIntegrationService::getTokenModel()?->current_tenant_id;

        return empty($tenantId) ? null : $tenantId;
    }

    protected static function lookupCacheKey(string $tenantId): string
    {
        return 'xero-integration:lookup:'.$tenantId.':'.Str::kebab(class_basename(static::class));
    }
}
