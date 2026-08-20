<?php

namespace Dcodegroup\XeroIntegration\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \League\OAuth2\Client\Token\AccessToken|null getToken(\Dcodegroup\XeroIntegration\Models\XeroToken|null $token = null)
 * @method static mixed getAccessTokenFromXero(\League\OAuth2\Client\Token\AccessToken $token)
 * @method static \Dcodegroup\XeroIntegration\Models\XeroToken|null getTokenModel()
 * @method static bool setXeroTenant(\Dcodegroup\XeroIntegration\Models\XeroToken $token)
 * @method static \Calcinai\OAuth2\Client\XeroTenant[] getXeroTenants(\Dcodegroup\XeroIntegration\Models\XeroToken $token, bool $useAuthEvent = false)
 * @method static \Dcodegroup\XeroIntegration\Models\XeroToken|null changeXeroTenant(\Calcinai\OAuth2\Client\XeroTenant $tenant)
 * @method static bool disconnectTenant(\Dcodegroup\XeroIntegration\Models\XeroToken $token)
 * @method static string getAuthUrl()
 * @method static \League\OAuth2\Client\Token\AccessTokenInterface getAccessTokenFromCode(string $code)
 * @method static \Dcodegroup\XeroIntegration\Models\XeroToken saveAccessTokenFromCode(string $code)
 *
 * @see \Dcodegroup\XeroIntegration\XeroIntegrationService
 */
class XeroIntegrationService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Dcodegroup\XeroIntegration\XeroIntegrationService::class;
    }
}
