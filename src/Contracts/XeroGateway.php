<?php

namespace Dcodegroup\XeroIntegration\Contracts;

use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;
use Illuminate\Support\Collection;

/**
 * Direct Xero API access. Records are exchanged as plain arrays with dates normalised to ISO 8601.
 */
interface XeroGateway
{
    /**
     * @return array<string, mixed>|null
     */
    public function find(XeroEndpoint $endpoint, string $id): ?array;

    /**
     * Fetch every page of matching records.
     *
     * @param  array<string, string>  $filters  Field => value, combined with AND.
     * @return Collection<int, array<string, mixed>>
     */
    public function list(XeroEndpoint $endpoint, array $filters = []): Collection;

    /**
     * Create the record, or update it when an id is given.
     *
     * @param  array<string, mixed>  $record
     * @return array<string, mixed> The record as saved.
     */
    public function save(XeroEndpoint $endpoint, array $record, ?string $id = null): array;

    /**
     * Call a sub-resource or action of a record, such as `Timesheets/{id}/Approve` or `Timesheets/{id}/Lines/{lineId}`.
     *
     * @param  string  $method  HTTP method: GET, POST, PUT or DELETE.
     * @param  array<int, string>  $segments  Path segments after the resource name; each is URL-encoded.
     * @param  array<int|string, mixed>  $body
     * @return array<string, mixed> The decoded response, with dates normalised.
     */
    public function action(XeroEndpoint $endpoint, string $method, array $segments, array $body = []): array;
}
