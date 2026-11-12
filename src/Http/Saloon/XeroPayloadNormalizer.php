<?php

namespace Dcodegroup\XeroIntegration\Http\Saloon;

use Carbon\CarbonImmutable;

/**
 * Payroll AU returns dates as `/Date(1439434356790+0000)/`. Convert them to ISO 8601 so
 * the data classes only ever see one format.
 */
class XeroPayloadNormalizer
{
    /**
     * @param  array<int|string, mixed>  $payload
     * @return array<int|string, mixed>
     */
    public static function normalize(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = static::normalize($value);
            } elseif (is_string($value)) {
                $payload[$key] = static::normalizeDate($value);
            }
        }

        return $payload;
    }

    protected static function normalizeDate(string $value): string
    {
        if (! preg_match('#^/Date\((-?\d+)(?:[+-]\d{4})?\)/$#', $value, $matches)) {
            return $value;
        }

        return CarbonImmutable::createFromTimestampMs((int) $matches[1], 'UTC')->format('Y-m-d\TH:i:s');
    }
}
