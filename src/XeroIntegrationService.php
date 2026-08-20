<?php

namespace Dcodegroup\XeroIntegration;

use Calcinai\OAuth2\Client\Provider\Xero;
use Calcinai\OAuth2\Client\XeroTenant;
use Dcodegroup\XeroIntegration\Exceptions\UnauthorizedXero;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class XeroIntegrationService
{
    /**
     * @throws UnauthorizedXero
     */
    public function getToken(?XeroToken $token = null): ?AccessToken
    {
        if (empty($token)) {
            $token = $this->getTokenModel();
        }

        if (empty($token)) {
            return null;
        }

        $oauth2Token = $token->toOAuth2Token();

        if ($oauth2Token->hasExpired()) {
            $oauth2Token = $this->getAccessTokenFromXero($oauth2Token);

            if (! XeroToken::isValidTokenFormat($oauth2Token)) {
                throw new UnauthorizedXero('Token is invalid or the provided token has invalid format!');
            }

            $creationData = array_merge($oauth2Token->jsonSerialize(), [
                'current_tenant_id' => $token->current_tenant_id
            ]);
            if (config('xero-integration.tenancy.enabled')) {
                $data['tenant_id'] = null;
                $sessionName = config('xero-integration.tenancy.session_name');
                if (! empty($sessionName) && Session::has($sessionName)) {
                    $tenantId = Session::get($sessionName);
                    $data['tenant_id'] = $tenantId;
                }
            }


            XeroToken::create($creationData);
        }

        return $oauth2Token;
    }

    public function getAccessTokenFromXero(AccessToken $token): mixed
    {
        return resolve(Xero::class)->getAccessToken('refresh_token', [
            'refresh_token' => $token->getRefreshToken(),
        ]);
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

    /**
     * Summary of setXeroTenant
     */
    public function setXeroTenant(XeroToken $token): bool
    {
        $xeroTenants = $this->getXeroTenants($token, true);

        if (count($xeroTenants) === 1) {
            $token->update([
                'current_tenant_id' => data_get($xeroTenants[0], 'tenantId'),
                'xero_tenant_type' => data_get($xeroTenants[0], 'tenantType'),
                'xero_tenant_name' => data_get($xeroTenants[0], 'tenantName'),
            ]);

            return true;
        }

        return false;
    }

    /**
     * Summary of getXeroTenants
     *
     * @return XeroTenant[]
     */
    public function getXeroTenants(XeroToken $token, bool $useAuthEvent = false): ?array
    {
        $params = null;

        if ($useAuthEvent) {
            $params['authEventId'] = $token->getAuthEventId();
        }

        $authToken = $this->getToken($token);

        return resolve(Xero::class)->getTenants($authToken);
    }

    public function changeXeroTenant(XeroTenant $tenant): ?XeroToken
    {
        $token = $this->getTokenModel();

        if (empty($token)) {
            return null;
        }

        $token->update([
            'current_tenant_id' => $tenant->tenantId,
            'xero_tenant_type' =>  $tenant->tenantType,
            'xero_tenant_name' =>  $tenant->tenantName,
        ]);

        return $token;
    }

    public function disconnectTenant(XeroToken $token): bool
    {
        // $oauthToken = $token->toOAuth2Token(); // ToDo: disconnect tenant with Xero.

        // $response = resolve(Xero::class)->disconnect($oauthToken);

        return $token->delete();
    }

    public function getAuthUrl(): string
    {
        if (empty(config('xero-integration.oauth.state'))) {
            throw new XeroIntegrationException('State is empty. Set the XERO_STATE environment variable.');
        }

        return resolve(Xero::class)->getAuthorizationUrl([
            'scope' => [config('xero-integration.oauth.scopes')],
            'state' => config('xero-integration.oauth.state'),
        ]);
    }

    /**
     * @throws IdentityProviderException
     * @throws UnauthorizedXero
     */
    public function getAccessTokenFromCode(string $code): AccessTokenInterface
    {
        $token = resolve(Xero::class)->getAccessToken('authorization_code', [
            'code' => $code,
        ]);

        if (! XeroToken::isValidTokenFormat($token)) {
            throw new UnauthorizedXero('Token is invalid or the provided token has invalid format!');
        }

        return $token;
    }

    public function saveAccessTokenFromCode(string $code): XeroToken
    {
        $token = $this->getAccessTokenFromCode($code);
        $data = $token->jsonSerialize();

        if (config('xero-integration.tenancy.enabled')) {
            $data['tenant_id'] = null;
            $sessionName = config('xero-integration.tenancy.session_name');
            if (! empty($sessionName) && Session::has($sessionName)) {
                $tenantId = Session::get($sessionName);
                $data['tenant_id'] = $tenantId;
            }
        }

        return XeroToken::create($data);
    }
}
