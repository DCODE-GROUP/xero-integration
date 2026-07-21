<?php

namespace DcodeGroup\XeroIntegration\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Saloon\Http\Auth\AccessTokenAuthenticator;

/**
 * @property int $id
 * @property int|null $tenant_id
 * @property string $id_token
 * @property string|null $token_type
 * @property string $access_token
 * @property string|null $refresh_token
 * @property string|null $scope
 * @property string|null $current_tenant_id
 * @property Carbon|null $expires
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

    protected $casts = [
        'expires' => 'datetime',
    ];

    public static function latestToken(): ?XeroToken
    {
        return self::latest('id')->first();
    }

    public function toAuthenticator(): AccessTokenAuthenticator
    {
        return new AccessTokenAuthenticator(
            $this->access_token,
            $this->refresh_token,
            $this->expires
        );
    }

    public static function isValidTokenFormat(array $data): bool
    {
        return ! Validator::make($data, [
            'id_token' => ['required', 'string'],
            'token_type' => ['required', 'string', 'in:Bearer'],
            'access_token' => ['required', 'string'],
            'refresh_token' => ['required', 'string'],
            'expires' => ['required', 'date'],
            'scope' => ['required', 'string'],
        ])->fails();
    }

    public function tenant(): ?BelongsTo
    {
        $model = config('xero-integration.tenancy.model');

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
