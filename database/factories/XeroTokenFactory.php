<?php

namespace DcodeGroup\XeroIntegration\Database\Factories;

use DcodeGroup\XeroIntegration\Models\XeroToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class XeroTokenFactory extends Factory
{
    protected $model = XeroToken::class;

    public function definition()
    {
        return [
            'id_token' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIn0.signature',
            'token_type' => 'Bearer',
            'access_token' => 'access_'.$this->faker->sha256(),
            'refresh_token' => 'refresh_'.$this->faker->sha256(),
            'scope' => 'openid email profile offline_access',
            'current_tenant_id' => $this->faker->uuid(),
            'expires' => now()->addSeconds(3600)->timestamp,
        ];
    }
}
