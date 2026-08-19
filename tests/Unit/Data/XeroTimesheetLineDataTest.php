<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroTimesheetLineData;
use Mockery;
use XeroPHP\Models\PayrollAU\Timesheet\TimesheetLine;

test('can instantiate XeroTimesheetLineData with all fields', function () {
    $units = [0.0, 7.6, 7.6, 7.6, 7.6, 7.6, 0.0];
    $updated = Carbon::parse('2024-01-08T10:00:00Z');

    $data = new XeroTimesheetLineData(
        EarningsRateID: 'earnings-rate-uuid',
        NumberOfUnits: $units,
        UpdatedDateUTC: $updated,
    );

    expect($data->EarningsRateID)->toBe('earnings-rate-uuid')
        ->and($data->NumberOfUnits)->toBe($units)
        ->and($data->UpdatedDateUTC)->toBe($updated);
});

test('optional fields default to null', function () {
    $data = new XeroTimesheetLineData;

    expect($data->EarningsRateID)->toBeNull()
        ->and($data->NumberOfUnits)->toBeNull()
        ->and($data->UpdatedDateUTC)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroTimesheetLineData;

    expect($data->toXeroArray())->toHaveKeys([
        'EarningsRateID', 'NumberOfUnits', 'UpdatedDateUTC',
    ]);
});

test('toXeroArray returns correct values', function () {
    $units = [7.6, 7.6, 7.6, 7.6, 7.6];
    $updated = Carbon::parse('2024-01-08T10:00:00Z');

    $data = new XeroTimesheetLineData(
        EarningsRateID: 'earnings-rate-uuid',
        NumberOfUnits: $units,
        UpdatedDateUTC: $updated,
    );

    $array = $data->toXeroArray();

    expect($array['EarningsRateID'])->toBe('earnings-rate-uuid')
        ->and($array['NumberOfUnits'])->toBe($units)
        ->and($array['UpdatedDateUTC'])->toBe($updated);
});

test('toXeroArray returns null for unset optional fields', function () {
    $array = (new XeroTimesheetLineData)->toXeroArray();

    expect($array['EarningsRateID'])->toBeNull()
        ->and($array['NumberOfUnits'])->toBeNull()
        ->and($array['UpdatedDateUTC'])->toBeNull();
});

test('fromXero maps xero model to XeroTimesheetLineData', function () {
    $units = [0.0, 7.6, 7.6, 7.6, 7.6, 7.6, 0.0];
    $updated = Carbon::parse('2024-01-08T10:00:00Z');

    $xeroLine = Mockery::mock(TimesheetLine::class);
    $xeroLine->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'EarningsRateID' => 'earnings-rate-uuid',
        'NumberOfUnits' => $units,
        'UpdatedDateUTC' => $updated,
        default => null,
    });
    $xeroLine->shouldReceive('offsetExists')->andReturn(true);

    $data = XeroTimesheetLineData::fromXero($xeroLine);

    expect($data)->toBeInstanceOf(XeroTimesheetLineData::class)
        ->and($data->EarningsRateID)->toBe('earnings-rate-uuid')
        ->and($data->NumberOfUnits)->toBe($units)
        ->and($data->UpdatedDateUTC)->toBe($updated);
});

test('fromXero returns null for missing optional fields', function () {
    $xeroLine = Mockery::mock(TimesheetLine::class);
    $xeroLine->shouldReceive('offsetGet')->andReturn(null);
    $xeroLine->shouldReceive('offsetExists')->andReturn(false);

    $data = XeroTimesheetLineData::fromXero($xeroLine);

    expect($data->EarningsRateID)->toBeNull()
        ->and($data->NumberOfUnits)->toBeNull()
        ->and($data->UpdatedDateUTC)->toBeNull();
});
