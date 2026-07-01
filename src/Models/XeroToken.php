<?php

namespace DcodeGroup\XeroIntegration\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * @property int $id
 * @property int|null $tenant_id
 * @property string $id_token
 * @property string|null $token_type
 * @property string $access_token
 * @property string|null $refresh_token
 * @property string|null $scope
 * @property string|null $current_tenant_id
 * @property int|null $expires
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class XeroToken extends Model
{
    use HasFactory;

    /**
     * Fields that are not mass assignable
     *
     * @var array<string>
     */
    protected $guarded = ['id'];

    public static function latestToken(): ?XeroToken
    {
        return self::latest('id')->first();
    }

    public function toOAuth2Token(): AccessToken
    {
        return new AccessToken($this->toArray());
    }

    public static function isValidTokenFormat(AccessTokenInterface $token): bool
    {
        return ! Validator::make($token->jsonSerialize(), [
            'id_token' => 'required',
            'token_type' => 'required',
            'access_token' => 'required',
            'refresh_token' => 'required',
            'expires' => 'required',
            'scope' => 'required',
        ])->fails();
    }

    public function tenant(): ?BelongsTo
    {
        $model = config('laravel-xero-oauth.multi_tenant_model');

        if ($model) {
            return $this->belongsTo($model, 'tenant_id');
        }

        return null;
    }

    // Add a global scope to ensure we only retrieve tokens for the authenticated tenant
    protected static function booted()
    {
        if (config('xero-integration.tenancy.enabled')) {
            static::addGlobalScope('tenant', function ($builder) {
                $tenantSessionName = config('xero-integration.tenancy.session_name');

                if (empty($tenantSessionName)) {
                    $tenantId = -1;
                } else {
                    $tenantId = session($tenantSessionName, -1);
                }

                $builder->where('tenant_id', $tenantId);
            });
        }
    }
}
