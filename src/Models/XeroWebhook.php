<?php

namespace Dcodegroup\XeroIntegration\Models;

use Dcodegroup\XeroIntegration\Enums\XeroWebhookStatusEnum;
use Dcodegroup\XeroIntegration\XeroApp;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use XeroPHP\Webhook;
use XeroPHP\Webhook\Event;
use Illuminate\Database\Eloquent\Collection;

/**
 * Logs incoming webhooks and their processing status
 *
 * @property int $id
 * @property string|array $payload
 * @property XeroWebhookStatusEnum $status
 * @property string $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Webhook $xero_webhook
 * @property-read Webhook $xeroWebhook 
 * @property-read Collection<int, \Dcodegroup\XeroIntegration\Models\XeroWebhookEvent> $events
 * @property-read int|null $events_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static> newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> query()
 */
class XeroWebhook extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'payload',
        'status',
        'message'
    ];

    protected $casts = [
        'payload' => 'json',
        'status' => XeroWebhookStatusEnum::class
    ];

    protected function xeroWebhook(): Attribute
    {
        return Attribute::make(function () {
            $payload = is_string($this->payload)
                ? $this->payload
                : json_encode($this->payload, JSON_THROW_ON_ERROR);

            return new WebHook(app(XeroApp::class), $payload);
        })->shouldCache();
    }

    /**
     * @return HasMany<XeroWebhookEvent, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(XeroWebhookEvent::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $xeroWebhook) {
            $xeroWebhook->status ??= XeroWebhookStatusEnum::PENDING;
        });

        // Create all the child records from the Xero event data
        static::created(function (self $xeroWebhook) {
            /** @var Event $event */
            foreach ($xeroWebhook->xeroWebhook->getEvents() as $event) {
                XeroWebhookEvent::create([
                    'xero_webhook_id' => $xeroWebhook->id,
                    'resource_url' => $event->getResourceUrl(),
                    'resource_id' => $event->getResourceId(),
                    'event_date' => $event->getEventDate(),
                    'event_type' => $event->getEventType(),
                    'event_category' => $event->getEventCategory(),
                    'xero_tenant_id' => $event->getTenantId(),
                    'xero_tenant_type' => $event->getTenantType(),
                ]);
            }
        });
    }
}
