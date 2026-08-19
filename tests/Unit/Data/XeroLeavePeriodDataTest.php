<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroLeavePeriodData;
use Dcodegroup\XeroIntegration\Enums\XeroLeavePeriodStatusEnum;
use Mockery;
use XeroPHP\Models\PayrollAU\LeaveApplication\LeavePeriod;

test('can instantiate XeroLeavePeriodData with required fields', function () {
    $endDate = Carbon::parse('2024-01-14');

    $data = new XeroLeavePeriodData(
        NumberOfUnits: 7.6,
        PayPeriodEndDate: $endDate,
    );

    expect($data->NumberOfUnits)->toBe(7.6)
        ->and($data->PayPeriodEndDate)->toBe($endDate);
});

test('optional fields default to null', function () {
    $data = new XeroLeavePeriodData(
        NumberOfUnits: 7.6,
        PayPeriodEndDate: Carbon::parse('2024-01-14'),
    );

    expect($data->PayPeriodStartDate)->toBeNull()
        ->and($data->LeavePeriodStatus)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroLeavePeriodData(
        NumberOfUnits: 7.6,
        PayPeriodEndDate: Carbon::parse('2024-01-14'),
    );

    expect($data->toXeroArray())->toHaveKeys([
        'NumberOfUnits', 'PayPeriodEndDate', 'PayPeriodStartDate', 'LeavePeriodStatus',
    ]);
});

test('toXeroArray returns correct values', function () {
    $startDate = Carbon::parse('2024-01-01');
    $endDate = Carbon::parse('2024-01-14');

    $data = new XeroLeavePeriodData(
        NumberOfUnits: 7.6,
        PayPeriodEndDate: $endDate,
        PayPeriodStartDate: $startDate,
        LeavePeriodStatus: XeroLeavePeriodStatusEnum::SCHEDULED,
    );

    $array = $data->toXeroArray();

    expect($array['NumberOfUnits'])->toBe(7.6)
        ->and($array['PayPeriodEndDate'])->toBe($endDate)
        ->and($array['PayPeriodStartDate'])->toBe($startDate)
        ->and($array['LeavePeriodStatus'])->toBe('SCHEDULED');
});

test('toXeroArray maps LeavePeriodStatus enum to Xero AU value', function ($status, $expected) {

    $data = new XeroLeavePeriodData(
        NumberOfUnits: 7.6,
        PayPeriodEndDate: Carbon::parse('2024-01-14'),
        LeavePeriodStatus: $status,
    );

    expect($data->toXeroArray()['LeavePeriodStatus'])->toBe($expected);
})->with([
    [XeroLeavePeriodStatusEnum::REQUESTED, 'REQUESTED'],
    [XeroLeavePeriodStatusEnum::SCHEDULED, 'SCHEDULED'],
    [XeroLeavePeriodStatusEnum::PROCESSED, 'PROCESSED'],
    [XeroLeavePeriodStatusEnum::REJECTED, 'REJECTED'],
]);

test('toXeroArray returns null LeavePeriodStatus when not set', function () {
    $data = new XeroLeavePeriodData(
        NumberOfUnits: 7.6,
        PayPeriodEndDate: Carbon::parse('2024-01-14'),
    );

    expect($data->toXeroArray()['LeavePeriodStatus'])->toBeNull();
});

test('fromXero maps xero model to XeroLeavePeriodData', function () {
    $startDate = Carbon::parse('2024-01-01');
    $endDate = Carbon::parse('2024-01-14');

    $xeroPeriod = Mockery::mock(LeavePeriod::class);
    $xeroPeriod->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'NumberOfUnits' => 7.6,
        'PayPeriodEndDate' => $endDate,
        'PayPeriodStartDate' => $startDate,
        'LeavePeriodStatus' => XeroLeavePeriodStatusEnum::PROCESSED,
        default => null,
    });
    $xeroPeriod->shouldReceive('offsetExists')->andReturn(true);

    $data = XeroLeavePeriodData::fromXero($xeroPeriod);

    expect($data)->toBeInstanceOf(XeroLeavePeriodData::class)
        ->and($data->NumberOfUnits)->toBe(7.6)
        ->and($data->PayPeriodEndDate)->toBe($endDate)
        ->and($data->PayPeriodStartDate)->toBe($startDate)
        ->and($data->LeavePeriodStatus)->toBe(XeroLeavePeriodStatusEnum::PROCESSED);
});

test('fromXero returns null for missing optional fields', function () {
    $endDate = Carbon::parse('2024-01-14');

    $xeroPeriod = Mockery::mock(LeavePeriod::class);
    $xeroPeriod->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'NumberOfUnits' => 7.6,
        'PayPeriodEndDate' => $endDate,
        default => null,
    });
    $xeroPeriod->shouldReceive('offsetExists')->andReturnUsing(fn ($key) => in_array($key, ['NumberOfUnits', 'PayPeriodEndDate'], true));

    $data = XeroLeavePeriodData::fromXero($xeroPeriod);

    expect($data->NumberOfUnits)->toBe(7.6)
        ->and($data->PayPeriodStartDate)->toBeNull()
        ->and($data->LeavePeriodStatus)->toBeNull();
});
