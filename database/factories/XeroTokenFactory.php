<?php

namespace Dcodegroup\XeroIntegration\Database\Factories;

use Dcodegroup\XeroIntegration\Models\XeroToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class XeroTokenFactory extends Factory
{
    protected $model = XeroToken::class;

    protected array $tokenDetails;

    public function definition()
    {
        $this->tokenDetails = [
            'sub' => $this->faker->uuid(),
            'iss' => 'https://identity.xero.com',
            'aud' => $this->faker->regexify('[a-z0-9]{32}'),
            'exp' => time() + 3600,
            'auth_time' => time(),
            'scope' => explode(' ', config('xero-integration.oauth.scopes')),
        ];

        return [
            'id_token' => $this->createIdToken(),
            'token_type' => 'Bearer',
            'access_token' => $this->createAccessToken(),
            'refresh_token' => 'refresh_'.$this->faker->sha256(),
            'scope' => config('xero-integration.oauth.scopes'),
            'current_tenant_id' => $this->faker->uuid(),
            'expires' => $this->tokenDetails['exp'],
        ];
    }

    protected function createAccessToken(): string
    {
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $payload = json_encode([
            'nbf' => $this->tokenDetails['auth_time'],
            'exp' => $this->tokenDetails['exp'],
            'iss' => $this->tokenDetails['iss'],
            'aud' => 'https://identity.xero.com/resources',
            'client_id' => $this->faker->uuid(),
            'sub' => $this->tokenDetails['sub'],
            'auth_time' => $this->tokenDetails['auth_time'],
            'xero_user_id' => $this->faker->uuid(),
            'global_session_id' => $this->faker->regexify('[a-z0-9]{32}'),
            'jti' => $this->faker->regexify('[a-z0-9]{32}'),
            'authentication_event_id' => $this->faker->uuid(),
            'scope' => $this->tokenDetails['scope'],
        ]);

        return base64_encode($header).'.'.base64_encode($payload).'.mock-signature';
    }

    public function createIdToken(): string
    {
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $payload = json_encode([
            'iss' => $this->tokenDetails['iss'],
            'sub' => $this->tokenDetails['sub'],
            'aud' => 'https://identity.xero.com/resources',
            'exp' => $this->tokenDetails['exp'],
            'iat' => $this->tokenDetails['auth_time'],
            'nbf' => $this->tokenDetails['auth_time'],
            'email' => $this->faker->email(),
            'given_name' => $this->faker->firstName(),
            'family_name' => $this->faker->lastName(),
            'name' => $this->faker->word(),
        ]);

        return base64_encode($header).'.'.base64_encode($payload).'.mock-signature';
    }
}
