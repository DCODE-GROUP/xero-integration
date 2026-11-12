<?php

namespace Dcodegroup\XeroIntegration\Services;

use Dcodegroup\XeroIntegration\Exceptions\XeroRateLimitExceededException;
use Dcodegroup\XeroIntegration\XeroQuery;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Counts direct API calls against the same per-tenant bucket the xero-php queries use.
 */
class XeroRateLimiter
{
    /**
     * Record one call.
     *
     * @throws XeroRateLimitExceededException When the bucket is already full.
     */
    public function consume(): void
    {
        $key = XeroQuery::getRateLimiterKey();

        if (RateLimiter::tooManyAttempts($key, $this->limit())) {
            throw XeroRateLimitExceededException::exceeded();
        }

        RateLimiter::hit($key, $this->decaySeconds());
    }

    /**
     * Fill the bucket after Xero itself reports the limit as hit.
     */
    public function exhaust(): void
    {
        RateLimiter::increment(XeroQuery::getRateLimiterKey(), $this->decaySeconds(), $this->limit());
    }

    protected function limit(): int
    {
        return (int) config('xero-integration.rate_limit.no');
    }

    protected function decaySeconds(): int
    {
        return (int) config('xero-integration.rate_limit.decay_seconds');
    }
}
