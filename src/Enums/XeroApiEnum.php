<?php

namespace Dcodegroup\XeroIntegration\Enums;

use Illuminate\Support\Str;

/**
 * The Xero APIs reachable through the direct (Saloon) connectors.
 *
 * Payroll AU 1.0 uses PascalCase JSON, bare record arrays and `/Date()/` dates. Payroll AU 2.0 (timesheets)
 * uses camelCase JSON, single-object bodies, lower-camel response keys and ISO 8601 dates.
 */
enum XeroApiEnum: string
{
    case ACCOUNTING = 'accounting';
    case PAYROLL_AU = 'payroll_au';
    case PAYROLL_AU_V2 = 'payroll_au_v2';

    public function baseUrl(): string
    {
        return match ($this) {
            self::ACCOUNTING => 'https://api.xero.com/api.xro/2.0',
            self::PAYROLL_AU => 'https://api.xero.com/payroll.xro/1.0',
            self::PAYROLL_AU_V2 => 'https://api.xero.com/payroll.xro/2.0',
        };
    }

    /**
     * Shape a single record into the request body the API expects.
     *
     * Payroll AU 1.0 takes a bare array of records, Accounting takes them under the resource name and
     * Payroll AU 2.0 takes the single record itself.
     *
     * @param  array<string, mixed>  $record
     * @return array<int|string, mixed>
     */
    public function wrapRecord(string $resource, array $record): array
    {
        return match ($this) {
            self::ACCOUNTING => [$resource => [$record]],
            self::PAYROLL_AU => [$record],
            self::PAYROLL_AU_V2 => $record,
        };
    }

    /**
     * The response key holding a list of records.
     */
    public function listKey(string $resource): string
    {
        return match ($this) {
            self::PAYROLL_AU_V2 => lcfirst($resource),
            default => $resource,
        };
    }

    /**
     * The response key holding a single record, or null when the API returns a one-item list instead.
     */
    public function itemKey(string $resource): ?string
    {
        return match ($this) {
            self::PAYROLL_AU_V2 => lcfirst(Str::singular($resource)),
            default => null,
        };
    }

    /**
     * Whether `list()` filters use the 2.0 `filter=a==x,b==y` query parameter and plain query parameters.
     */
    public function usesQueryFilters(): bool
    {
        return $this === self::PAYROLL_AU_V2;
    }
}
