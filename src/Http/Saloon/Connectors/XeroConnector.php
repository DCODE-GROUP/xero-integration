<?php

namespace Dcodegroup\XeroIntegration\Http\Saloon\Connectors;

use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Exceptions\UnauthorizedXero;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegrationService;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Dcodegroup\XeroIntegration\Services\XeroRateLimiter;
use Saloon\Contracts\Authenticator;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;

/**
 * Authenticated, rate-limited access to one Xero API for the connected tenant.
 *
 * A 401 refreshes the token once and replays the request.
 */
class XeroConnector extends Connector
{
    public ?int $tries = 2;

    public ?int $retryInterval = 0;

    protected XeroToken $tokenModel;

    protected string $accessToken;

    public function __construct(protected XeroApiEnum $api, ?XeroToken $tokenModel = null)
    {
        $tokenModel ??= XeroIntegrationService::getTokenModel();

        if (empty($tokenModel)) {
            throw new XeroIntegrationException('No Xero token found in the database. Please connect your Xero account.');
        }

        $oauthToken = XeroIntegrationService::getToken($tokenModel);

        if (empty($oauthToken)) {
            throw new XeroIntegrationException('Unable to retrieve a valid OAuth token for Xero integration.');
        }

        $this->tokenModel = $tokenModel;
        $this->accessToken = $oauthToken->getToken();
    }

    public function resolveBaseUrl(): string
    {
        return $this->api->baseUrl();
    }

    protected function defaultAuth(): ?Authenticator
    {
        return new TokenAuthenticator($this->accessToken);
    }

    /**
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Xero-tenant-id' => (string) $this->tokenModel->current_tenant_id,
        ];
    }

    public function boot(PendingRequest $pendingRequest): void
    {
        $pendingRequest->middleware()->onRequest(function (PendingRequest $request): PendingRequest {
            app(XeroRateLimiter::class)->consume();

            return $request;
        });
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        if (! $exception instanceof RequestException || $exception->getResponse()->status() !== 401) {
            return false;
        }

        $refreshed = XeroIntegrationService::getToken($this->tokenModel, true, $this->accessToken);

        if (empty($refreshed)) {
            throw new UnauthorizedXero('Unable to refresh the Xero token.');
        }

        $this->accessToken = $refreshed->getToken();
        $this->tokenModel = $this->tokenModel->fresh() ?? $this->tokenModel;
        $this->authenticate($this->defaultAuth());

        return true;
    }
}
