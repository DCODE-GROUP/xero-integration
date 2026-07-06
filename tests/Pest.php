<?php

use DcodeGroup\XeroIntegration\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;

uses(
    TestCase::class,
    RefreshDatabase::class,
    WithWorkbench::class)
    ->in(__DIR__);
