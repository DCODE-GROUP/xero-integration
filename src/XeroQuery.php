<?php

namespace Dcodegroup\XeroIntegration;

use Dcodegroup\XeroIntegration\Exceptions\XeroConfigException;
use Dcodegroup\XeroIntegration\Exceptions\XeroRateLimitExceededException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Override;
use XeroPHP\Remote\Collection;
use XeroPHP\Remote\Exception\RateLimitExceededException;
use XeroPHP\Remote\Query;
use XeroPHP\Remote\Response;

class XeroQuery extends Query
{
    protected int $limit;

    protected int $decaySeconds;

    public function __construct(XeroApp $app)
    {
        parent::__construct($app);

        $this->limit = config('xero-integration.rate_limit.no');
        $this->decaySeconds = config('xero-integration.rate_limit.decay_seconds');
    }

    /**
     * @return Collection
     *
     * @throws XeroRateLimitExceededException
     */
    #[Override]
    public function execute()
    {
        $result = RateLimiter::attempt(
            key: self::getRateLimiterKey(),
            maxAttempts: $perMinute = $this->limit,
            callback: fn () => $this->parentExecute(),
            decaySeconds: $this->decaySeconds,
        );

        if ($result == false) {
            throw new XeroRateLimitExceededException('Xero API rate limit exceeded. Please try again later.');
        }

        return $result;
    }

    public static function getRateLimiterKey(): string
    {
        if (config('xero-integration.tenancy.enabled') && ! empty(config('xero-integration.tenancy.tenant_resolver'))) {
            $tenant = self::getTenant();

            if (empty($tenant)) {
                throw new XeroConfigException('Xero integration tenancy is enabled, but no tenant could be resolved. Please check your configuration.');
            }

            return self::class.':'.$tenant->getKey();
        }

        return self::class;
    }

    public static function getTenant(): ?Model
    {
        if (empty(config('xero-integration.tenancy.tenant_resolver'))) {
            return null;
        }

        $config = config('xero-integration.tenancy.tenant_resolver');

        if (! is_callable($config)) {
            return null;
        }

        return app()->call($config);
    }

    protected function parentExecute()
    {
        return parent::execute();
    }

    /**
     * @return Response|null
     *
     * @throws XeroRateLimitExceededException
     */
    #[Override]
    public function getResponse()
    {
        try {
            $response = $this->getParentResponse();
        } catch (RateLimitExceededException $e) {
            $this->handleRateLimitExceeded();
        }

        if (in_array($response->getStatus(), [Response::STATUS_RATE_LIMIT_EXCEEDED, Response::STATUS_TOO_MANY_REQUESTS])) {
            $this->handleRateLimitExceeded();
        }

        return $response;
    }

    protected function getParentResponse()
    {
        return parent::getResponse();
    }

    /**
     * Summary of handleRateLimitExceeded
     *
     * @return never
     *
     * @throws XeroRateLimitExceededException
     */
    protected function handleRateLimitExceeded(): void
    {
        $result = RateLimiter::increment(
            key: self::getRateLimiterKey(),
            decaySeconds: $this->decaySeconds,
            amount: $this->limit
        );

        throw new XeroRateLimitExceededException('Xero API rate limit exceeded. Please try again later.');
    }
}
