<?php

namespace Dcodegroup\XeroIntegration\Tests\Feature;

use Dcodegroup\XeroIntegration\Enums\XeroWebhookStatusEnum;
use Dcodegroup\XeroIntegration\Http\Controllers\XeroWebhookController;
use Dcodegroup\XeroIntegration\Http\Requests\XeroWebhookRequest;
use Dcodegroup\XeroIntegration\Jobs\AbstractXeroWebhookEventJob;
use Dcodegroup\XeroIntegration\Jobs\XeroWebhookProcessJob;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Dcodegroup\XeroIntegration\Models\XeroWebhook;
use Dcodegroup\XeroIntegration\XeroApp;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Workbench\App\Models\Tenant;

beforeEach(function () {
    config()->set('xero-integration.tenancy.enabled', false);
    config()->set('xero-integration.tenancy.session_name', 'xero_current_tenant_id');

    $this->webhookSecret = 'webhook-test-secret';
    $this->mock(XeroApp::class, function (MockInterface $mock) {
        $mock->shouldReceive('getConfigOption')
            ->with('webhook', 'signing_key')
            ->andReturn($this->webhookSecret);
    });
});

function webhookPayload(): array
{
    return [
        'events' => [[
            'resourceUrl' => 'https://api.xero.com/api.xro/2.0/Contacts/contact-1',
            'resourceId' => 'contact-1',
            'eventDateUtc' => '2026-08-19T00:00:00Z',
            'eventType' => 'CREATE',
            'eventCategory' => 'CONTACT',
            'tenantId' => 'xero-tenant-1',
            'tenantType' => 'ORGANISATION',
        ]],
        'firstEventSequence' => 1,
        'lastEventSequence' => 1,
    ];
}

function postWebhook($test, array $payload, string $secret)
{
    $body = json_encode($payload, JSON_THROW_ON_ERROR);
    $signature = base64_encode(hash_hmac('sha256', $body, $secret, true));

    $request = XeroWebhookRequest::create('/webhooks/xero', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_XERO_SIGNATURE' => $signature,
    ], $body);

    $response = app(XeroWebhookController::class)($request);
    app()->terminate();

    return $response;
}

function postWebhookRoute($test, array $payload, string $signature)
{
    $body = json_encode($payload, JSON_THROW_ON_ERROR);

    return $test->call('POST', '/webhooks/xero', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_XERO_SIGNATURE' => $signature,
    ], $body);
}

test('returns 200 for a webhook with a valid signature', function () {
    Queue::fake();

    $body = json_encode(webhookPayload(), JSON_THROW_ON_ERROR);
    $signature = base64_encode(hash_hmac('sha256', $body, $this->webhookSecret, true));

    postWebhookRoute($this, webhookPayload(), $signature)
        ->assertStatus(200);
});

test('returns 401 for a webhook with an invalid signature', function () {
    postWebhookRoute($this, webhookPayload(), 'invalid-signature')
        ->assertStatus(401);
});

test('persists a webhook and its events without tenancy', function () {
    Queue::fake();

    $response = postWebhook($this, webhookPayload(), $this->webhookSecret);

    expect($response->status())->toBe(200);

    $webhook = XeroWebhook::first();
    expect($webhook)->not->toBeNull()
        ->and($webhook->status)->toBe(XeroWebhookStatusEnum::PENDING)
        ->and($webhook->tenant_id)->toBeNull()
        ->and($webhook->events)->toHaveCount(1)
        ->and($webhook->events->first()->status)->toBe(XeroWebhookStatusEnum::PENDING);

    Queue::assertPushed(XeroWebhookProcessJob::class);
});

test('persists a webhook for the current tenant when tenancy is enabled', function () {
    config()->set('xero-integration.tenancy.enabled', true);
    $targetTenant = Tenant::factory()->create();
    XeroToken::create([
        'tenant_id' => $targetTenant->id,
        'id_token' => fake()->text(),
        'access_token' => fake()->text(),
        'current_tenant_id' => 'xero-tenant-1',
    ]);

    $irrelevantTenant = Tenant::factory()->create();
    XeroToken::create([
        'tenant_id' => $irrelevantTenant->id,
        'id_token' => fake()->text(),
        'access_token' => fake()->text(),
        'current_tenant_id' => 'xero-tenant-2',
    ]);
    Queue::fake();

    $response = postWebhook($this, webhookPayload(), $this->webhookSecret);

    expect($response->status())->toBe(200);
    expect(XeroWebhook::first()->tenant_id)->toBe($targetTenant->id);
});

test('event processing moves the event and webhook from pending to successful', function () {
    $webhook = XeroWebhook::create(['payload' => webhookPayload()]);
    $event = $webhook->events->first();

    $event->setStatus(XeroWebhookStatusEnum::PROCESSING);
    expect($event->fresh()->status)->toBe(XeroWebhookStatusEnum::PROCESSING)
        ->and($webhook->fresh()->status)->toBe(XeroWebhookStatusEnum::PROCESSING);

    (new TestWebhookEventJob($event->fresh()))->handle();

    expect($event->fresh()->status)->toBe(XeroWebhookStatusEnum::SUCCESSFUL)
        ->and($webhook->fresh()->status)->toBe(XeroWebhookStatusEnum::SUCCESSFUL);
});

test('failed event processing marks the webhook as failed', function () {
    $webhook = XeroWebhook::create(['payload' => webhookPayload()]);
    $event = $webhook->events->first();
    $job = new TestWebhookEventJob($event);

    $job->fail(new \RuntimeException('processing failed'));

    expect($event->fresh()->status)->toBe(XeroWebhookStatusEnum::FAILURE)
        ->and($webhook->fresh()->status)->toBe(XeroWebhookStatusEnum::FAILURE);
});

class TestWebhookEventJob extends AbstractXeroWebhookEventJob
{
    public function handleEvent(): void {}
}
