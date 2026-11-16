<?php

use Dcodegroup\XeroIntegration\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Saloon\Http\Faking\MockClient;

uses(
    TestCase::class,
    RefreshDatabase::class,
    WithWorkbench::class)
    ->in(__DIR__);

/**
 * Replace the global Saloon mock client, so every connector request is faked.
 *
 * @param  array<class-string|string, mixed>  $responses
 */
function fakeSaloon(array $responses): MockClient
{
    MockClient::destroyGlobal();

    return MockClient::global($responses);
}
