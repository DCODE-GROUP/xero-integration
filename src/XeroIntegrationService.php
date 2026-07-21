<?php

namespace DcodeGroup\XeroIntegration;

use DcodeGroup\XeroIntegration\Exceptions\UnauthorizedXero;
use DcodeGroup\XeroIntegration\Http\Connectors\XeroConnector;
use DcodeGroup\XeroIntegration\Models\XeroToken;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Saloon\Http\Auth\AccessTokenAuthenticator;

class XeroIntegrationService
{
    /**
     * @throws UnauthorizedXero
     */
    public function getAuthenticator(?XeroToken $token = null): ?AccessTokenAuthenticator
    {
        if (empty($token)) {
            $token = $this->getTokenModel();
        }

        if (empty($token)) {
            return null;
        }

        $authenticator = $token->toAuthenticator();

        if ($authenticator->hasExpired()) {
            $authenticator = $this->refreshAccessToken($authenticator);

            $this->saveAccessToken($authenticator, $token->current_tenant_id);
        }

        return $authenticator;
    }

    public function refreshAccessToken(AccessTokenAuthenticator $authenticator): mixed
    {
        return resolve(XeroConnector::class)->refreshAccessToken($authenticator);
    }

    public function getTokenModel(): ?XeroToken
    {
        if (! Schema::hasTable((new XeroToken)->getTable())) {
            return null;
        }

        $token = XeroToken::latestToken();

        if (empty($token)) {
            return null;
        }

        return $token;
    }

    public function changeXeroTenant(string $tenantId): ?XeroToken
    {
        $token = $this->getTokenModel();

        if (empty($token)) {
            return null;
        }

        $token->current_tenant_id = $tenantId;
        $token->save();

        return $token;
    }

    public function getAuthUrl(): string
    {
        return resolve(XeroConnector::class)->getAuthorizationUrl();
    }

    public function saveAccessTokenFromCode(string $code): XeroToken
    {
        $authenticator = resolve(XeroConnector::class)->getAccessToken($code);

        return $this->saveAccessToken($authenticator);
    }

    protected function saveAccessToken(AccessTokenAuthenticator $authenticator, ?string $tenantId = null): XeroToken
    {
        $data = [
            'id_token' => $authenticator->getAccessToken(),
            'token_type' => 'Bearer',
            'access_token' => $authenticator->getAccessToken(),
            'refresh_token' => $authenticator->getRefreshToken(),
            'expires' => $authenticator->getExpiresAt(),
            'scope' => config('xero-integration.oauth.scopes'),
        ];

        if (config('xero-integration.tenancy.enabled')) {
            $data['tenant_id'] = $tenantId;
            if (empty($tenantId)) {
                $sessionName = config('xero-integration.tenancy.session_name');
                if (! empty($sessionName) && Session::has($sessionName)) {
                    $data['tenant_id'] = Session::get($sessionName);
                }
            }
        }

        if (! XeroToken::isValidTokenFormat($data)) {
                throw new UnauthorizedXero('Token is invalid or the provided token has invalid format!');
            }

        return XeroToken::create($data);
    }
}
