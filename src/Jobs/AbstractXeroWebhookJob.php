<?php

namespace Dcodegroup\XeroIntegration\Jobs;

use Dcodegroup\XeroIntegration\Events\XeroWebhookProcessingFailedEvent;
use Dcodegroup\XeroIntegration\XeroApp;
use Dcodegroup\XeroIntegration\XeroQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

abstract class AbstractXeroWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected XeroApp $xeroApp;

    public function __construct()
    {
        $this->onQueue(config('xero-integration.webhooks.queue'));

        $this->xeroApp = app(XeroApp::class);
    }

    public function backoff(): array
    {
        return config('xero-integration.webhooks.backoffs');
    }

    /**
     * Set the desired delay in seconds for the job.
     *
     * @return $this
     */
    public function delay(\DateTimeInterface|\DateInterval|array|int|null $delay)
    {
        $rateLimiterDelay = RateLimiter::availableIn(XeroQuery::getRateLimiterKey());

        if (empty($delay) || $rateLimiterDelay > $delay) {
            $delay = $rateLimiterDelay;
        }

        $this->delay = $delay;

        return $this;
    }

    public function failed(Throwable $exception)
    {
        if (property_exists($this, 'webhook')) {
            XeroWebhookProcessingFailedEvent::dispatch($this->webhook, $exception->getMessage());
        }

        report($exception);

    }
}
