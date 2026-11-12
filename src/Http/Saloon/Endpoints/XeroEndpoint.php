<?php

namespace Dcodegroup\XeroIntegration\Http\Saloon\Endpoints;

use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use InvalidArgumentException;

/**
 * Describes where a Xero resource lives, so one set of generic requests can serve every data type.
 */
final readonly class XeroEndpoint
{
    /**
     * @param  array<int, string>  $guidFilters  Filter fields Xero expects as GUIDs: `Guid("...")` in 1.0 `where` clauses and `filter=field==guid` in 2.0.
     * @param  string|null  $listKey  Dot path to the records in a list response, when it is not the resource name (such as `PayItems.EarningsRates`).
     */
    public function __construct(
        public XeroApiEnum $api,
        public string $resource,
        public string $idKey,
        public array $guidFilters = [],
        public bool $paginated = false,
        public ?string $listKey = null,
    ) {}

    public function resolvedListKey(): string
    {
        return $this->listKey ?? $this->api->listKey($this->resource);
    }

    /**
     * The URL-encoded path of a record or sub-resource below this resource, such as `/{id}/Lines/{lineId}`.
     *
     * @param  array<int, string>  $segments
     *
     * @throws InvalidArgumentException When a segment is empty or a dot segment, which a client would resolve away.
     */
    public function path(array $segments = []): string
    {
        $path = '/'.$this->resource;

        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                throw new InvalidArgumentException('Invalid Xero path segment.');
            }

            $path .= '/'.rawurlencode($segment);
        }

        return $path;
    }
}
