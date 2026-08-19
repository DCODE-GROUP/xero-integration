<?php

namespace Dcodegroup\XeroIntegration\Models;

use Dcodegroup\XeroIntegration\Enums\XeroWebhookStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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
 * @property-read XeroWebhook $xero_webhook
 * @property-read XeroWebhook $xeroWebhook
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
        'message',
    ];

    protected $casts = [
        'status' => XeroWebhookStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $event) {
            $event->status ??= XeroWebhookStatusEnum::PENDING;
        });
    }

    public function xeroWebhook()
    {
        return $this->belongsTo(XeroWebhook::class);
    }

    public function setStatus(XeroWebhookStatusEnum $newStatus)
    {
        $this->update(['status' => $newStatus->value]);
        $this->loadMissing('xeroWebhook');
        switch ($newStatus) {
            case XeroWebhookStatusEnum::FAILURE:
                $this->xeroWebhook->update(['status' => $newStatus->value]);
                break;
            case XeroWebhookStatusEnum::SUCCESSFUL:
                if ($this->xeroWebhook->events()->where('status', $newStatus->value)->count() == $this->xeroWebhook->events()->count()) {
                    $this->xeroWebhook->update(['status' => $newStatus->value]);
                }
                break;
            case XeroWebhookStatusEnum::PROCESSING:
                if ($this->xeroWebhook->status == XeroWebhookStatusEnum::PENDING) {
                    $this->xeroWebhook->update(['status' => $newStatus->value]);
                }
                break;
        }
    }
}
