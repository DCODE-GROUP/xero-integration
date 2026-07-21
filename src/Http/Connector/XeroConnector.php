<?php

namespace DcodeGroup\XeroIntegration\Http\Connectors;

use DcodeGroup\XeroIntegration\Exceptions\XeroIntegrationException;
use DcodeGroup\XeroIntegration\Facades\XeroIntegrationService;
use DcodeGroup\XeroIntegration\Models\XeroToken;
use Illuminate\Support\Facades\Cache;
use Saloon\Helpers\OAuth2\OAuthConfig;
use Saloon\Http\Connector;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Limit;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;
use Saloon\Traits\Auth\AuthenticatesRequests;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;
use Saloon\Traits\Plugins\AcceptsJson;

/**
 * Xero Accounting API
 */
class XeroConnector extends Connector
{
    use AcceptsJson;
    use AuthenticatesRequests;
    use AuthorizationCodeGrant;
    use HasRateLimits;

    protected XeroToken $xeroToken;

    public function resolveBaseUrl(): string
    {
        return 'https://api.xero.com/api.xro/2.0';
    }

    public function defaultOauthConfig(): OAuthConfig
    {
        return OAuthConfig::make()
            ->setClientId(config('xero-integration.oauth.client_id'))
            ->setClientSecret(config('xero-integration.oauth.client_secret'))
            ->setDefaultScopes(config('xero-integration.oauth.scopes'))
            ->setAuthorizeEndpoint(config('xero-integration.oauth.authorization_url'))
            ->setTokenEndpoint(config('xero-integration.oauth.token_url'))
            ->setRedirectUri(url()->route('xero.callback'));
    }

    /**
     * Default Request Headers
     *
     * @return array<string, mixed>
     */
    protected function defaultHeaders(): array
    {
        $xeroToken = $this->getTokenModel();

        return [
            'Authorization' => 'Bearer '.$xeroToken?->getAccessToken(),
            'Content-Type' => 'application/json',
            'Xero-tenant-id' => $xeroToken?->current_tenant_id,
        ];
    }

    protected function resolveLimits(): array
    {
        return [
            Limit::allow(60)->everyMinute()->name('MinLimit-'.$this->getTokenModel()->current_tenant_id)->sleep,
        ];
    }

    protected function resolveRateLimitStore(): RateLimitStore
    {
        return new LaravelCacheStore(Cache::store('xero-integration.rate_limit.store'));
    }

    protected function getTokenModel(): XeroToken
    {
        if (isset($this->xeroToken)) {
            return $this->xeroToken;
        }

        $tokenModel = XeroIntegrationService::getTokenModel();

        if (empty($tokenModel)) {
            throw new XeroIntegrationException('No Xero token found in the database. Please connect your Xero account.');
        }

        $this->xeroToken = $tokenModel;

        return $tokenModel;
    }
}
