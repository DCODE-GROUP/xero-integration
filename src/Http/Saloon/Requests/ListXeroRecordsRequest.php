<?php

namespace Dcodegroup\XeroIntegration\Http\Saloon\Requests;

use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;
use InvalidArgumentException;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListXeroRecordsRequest extends Request
{
    /**
     * Query parameters the request sets itself, so they cannot be passed as filter fields.
     */
    protected const RESERVED_FIELDS = ['filter', 'where', 'page', 'order'];

    protected Method $method = Method::GET;

    /**
     * @param  array<string, string>  $filters  Field => value, combined with AND. For 2.0 APIs, GUID filters
     *                                          go in the `filter` parameter and the rest are plain query parameters.
     */
    public function __construct(
        protected XeroEndpoint $endpoint,
        protected array $filters = [],
        protected int $page = 1,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.$this->endpoint->resource;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        $query = $this->endpoint->api->usesQueryFilters()
            ? $this->queryFilters()
            : $this->whereQuery();

        if ($this->endpoint->paginated) {
            $query['page'] = $this->page;
        }

        return $query;
    }

    /**
     * @return array<string, string>
     */
    protected function whereQuery(): array
    {
        if (empty($this->filters)) {
            return [];
        }

        $clauses = [];

        foreach ($this->filters as $field => $value) {
            $this->assertValidField($field);

            $value = (string) $value;

            if (in_array($field, $this->endpoint->guidFilters, true)) {
                $this->assertGuid($field, $value);

                $clauses[] = "{$field}=Guid(\"{$value}\")";

                continue;
            }

            $value = str_replace(['\\', '"'], ['\\\\', '\\"'], $value);

            $clauses[] = "{$field}==\"{$value}\"";
        }

        return ['where' => implode(' AND ', $clauses)];
    }

    /**
     * @return array<string, string>
     */
    protected function queryFilters(): array
    {
        $query = [];
        $guidClauses = [];

        foreach ($this->filters as $field => $value) {
            $this->assertValidField($field);

            $value = (string) $value;

            if (in_array($field, $this->endpoint->guidFilters, true)) {
                $this->assertGuid($field, $value);

                $guidClauses[] = "{$field}=={$value}";

                continue;
            }

            $query[$field] = $value;
        }

        if (! empty($guidClauses)) {
            $query['filter'] = implode(',', $guidClauses);
        }

        return $query;
    }

    protected function assertValidField(string|int $field): void
    {
        if (! preg_match('/^[A-Za-z][A-Za-z0-9.]*$/D', (string) $field) || in_array(strtolower((string) $field), self::RESERVED_FIELDS, true)) {
            throw new InvalidArgumentException("Invalid Xero filter field '{$field}'.");
        }
    }

    protected function assertGuid(string $field, string $value): void
    {
        if (! preg_match('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}$/iD', $value)) {
            throw new InvalidArgumentException("Filter '{$field}' must be a GUID.");
        }
    }
}
