<?php

namespace Dcodegroup\XeroIntegration\Models;

use Dcodegroup\XeroIntegration\Enums\XeroWebhookStatusEnum;
use Dcodegroup\XeroIntegration\XeroApp;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use XeroPHP\Webhook;

/**
 * Logs incoming webhook events and their processing status
 *
 * @property int $id
 * @property int $xero_webhook_id 
 * @property string $resource_url
 * @property string $resource_id
 * @property string $event_type
 * @property string $event_category
 * @property string $xero_tenant_id
 * @property string $xero_tenant_type
 * @property Carbon|null $event_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property XeroWebhookStatusEnum $status
 * @property string $message
 * @property-read Webhook $xero_webhook
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static> newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> query()
 */
class XeroWebhookEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'xero_webhook_id',
        'resource_url',
        'resource_id',
        'event_date',
        'event_type',
        'event_category',
        'xero_tenant_id',
        'xero_tenant_type',
        'tenant_id',
        'status',
        'message'
    ];

    protected $casts = [
        'status' => XeroWebhookStatusEnum::class
    ];

    public function xeroWebhook()
    {
        return $this->belongsTo(XeroWebhook::class);
    }
}
