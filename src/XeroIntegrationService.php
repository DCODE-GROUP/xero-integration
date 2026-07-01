<?php

namespace DcodeGroup\XeroIntegration;

use Calcinai\OAuth2\Client\Provider\Xero;
use DcodeGroup\XeroIntegration\Exceptions\UnauthorizedXero;
use DcodeGroup\XeroIntegration\Models\XeroToken;
use Illuminate\Support\Facades\Schema;
use League\OAuth2\Client\Token\AccessToken;

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

            XeroToken::create(array_merge($oauth2Token->jsonSerialize(), ['current_tenant_id' => $token->current_tenant_id]));
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
}
