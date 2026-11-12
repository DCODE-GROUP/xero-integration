<?php

namespace Dcodegroup\XeroIntegration\Data\Traits;

use Dcodegroup\XeroIntegration\Contracts\XeroGateway;
use Dcodegroup\XeroIntegration\Data\Contracts\HasXeroEndpoint;
use Illuminate\Support\Collection;

/**
 * Read-only direct API access for a data type. Add it and implement `getXeroEndpoint()`, and the type
 * can `find()` and `list()` without touching xero-php.
 *
 * @phpstan-require-implements HasXeroEndpoint
 */
trait ReadsFromXeroApi
{
    public static function find(string $xeroId): ?self
    {
        $record = app(XeroGateway::class)->find(static::getXeroEndpoint(), $xeroId);

        return $record === null ? null : static::fromXeroArray($record);
    }

    /**
     * @param  array<string, string>  $filters  Field => value, combined with AND.
     * @return Collection<int, self>
     */
    public static function list(array $filters = []): Collection
    {
        return app(XeroGateway::class)
            ->list(static::getXeroEndpoint(), $filters)
            ->map(fn (array $record): self => static::fromXeroArray($record));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromXeroArray(array $payload): self
    {
        return static::from($payload);
    }
}
