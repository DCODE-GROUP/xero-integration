<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroTimesheetData;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroTimesheetLineData;
use Dcodegroup\XeroIntegration\Enums\XeroTimesheetStatusEnum;
use Illuminate\Support\Collection;
use Mockery;
use XeroPHP\Models\PayrollAU\Timesheet;
use XeroPHP\Models\PayrollAU\Timesheet\TimesheetLine;

test('can instantiate XeroTimesheetData with required fields', function () {
    $startDate = Carbon::parse('2024-01-01');
    $endDate = Carbon::parse('2024-01-07');
    $lines = new Collection([
        new XeroTimesheetLineData(EarningsRateID: 'earnings-rate-uuid', NumberOfUnits: [7.6]),
    ]);

    $data = new XeroTimesheetData(
        EmployeeID: 'employee-uuid',
        StartDate: $startDate,
        EndDate: $endDate,
        TimesheetLines: $lines,
    );

    expect($data->EmployeeID)->toBe('employee-uuid')
        ->and($data->StartDate)->toBe($startDate)
        ->and($data->EndDate)->toBe($endDate)
        ->and($data->TimesheetLines)->toBe($lines);
});

test('optional fields default to null', function () {
    $data = new XeroTimesheetData(
        EmployeeID: 'employee-uuid',
        StartDate: Carbon::parse('2024-01-01'),
        EndDate: Carbon::parse('2024-01-07'),
        TimesheetLines: new Collection,
    );

    expect($data->TimesheetID)->toBeNull()
        ->and($data->Status)->toBeNull()
        ->and($data->Hours)->toBeNull()
        ->and($data->UpdatedDateUTC)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroTimesheetData(
        EmployeeID: 'employee-uuid',
        StartDate: Carbon::parse('2024-01-01'),
        EndDate: Carbon::parse('2024-01-07'),
        TimesheetLines: new Collection,
    );

    expect($data->toXeroArray())->toHaveKeys([
        'EmployeeID', 'StartDate', 'EndDate', 'TimesheetLines',
        'TimesheetID', 'Status', 'Hours', 'UpdatedDateUTC',
    ]);
});

test('toXeroArray returns correct values', function () {
    $startDate = Carbon::parse('2024-01-01');
    $endDate = Carbon::parse('2024-01-07');
    $updated = Carbon::parse('2024-01-08T10:00:00Z');
    $line = new XeroTimesheetLineData(EarningsRateID: 'earnings-rate-uuid', NumberOfUnits: [7.6]);

    $data = new XeroTimesheetData(
        EmployeeID: 'employee-uuid',
        StartDate: $startDate,
        EndDate: $endDate,
        TimesheetLines: new Collection([$line]),
        TimesheetID: 'timesheet-uuid',
        Status: XeroTimesheetStatusEnum::APPROVED,
        Hours: 38,
        UpdatedDateUTC: $updated,
    );

    $array = $data->toXeroArray();

    expect($array['EmployeeID'])->toBe('employee-uuid')
        ->and($array['StartDate'])->toBe($startDate)
        ->and($array['EndDate'])->toBe($endDate)
        ->and($array['TimesheetLines'])->toBe([$line->toXeroArray()])
        ->and($array['TimesheetID'])->toBe('timesheet-uuid')
        ->and($array['Hours'])->toBe(38)
        ->and($array['UpdatedDateUTC'])->toBe($updated);
});

test('toXeroArray maps Status enum to Xero AU value', function ($status, $expected) {
    $data = new XeroTimesheetData(
        EmployeeID: 'employee-uuid',
        StartDate: Carbon::parse('2024-01-01'),
        EndDate: Carbon::parse('2024-01-07'),
        TimesheetLines: new Collection,
        Status: $status,
    );

    expect($data->toXeroArray()['Status'])->toBe($expected);
})->with([
    [XeroTimesheetStatusEnum::DRAFT, Timesheet::STATUS_DRAFT],
    [XeroTimesheetStatusEnum::PROCESSED, Timesheet::STATUS_PROCESSED],
    [XeroTimesheetStatusEnum::APPROVED, Timesheet::STATUS_APPROVED],
]);

test('toXeroArray returns null Status when not set', function () {
    $data = new XeroTimesheetData(
        EmployeeID: 'employee-uuid',
        StartDate: Carbon::parse('2024-01-01'),
        EndDate: Carbon::parse('2024-01-07'),
        TimesheetLines: new Collection,
    );

    expect($data->toXeroArray()['Status'])->toBeNull();
});

test('fromXero maps xero model to XeroTimesheetData', function () {
    $startDate = Carbon::parse('2024-01-01');
    $endDate = Carbon::parse('2024-01-07');
    $updated = Carbon::parse('2024-01-08T10:00:00Z');

    $xeroLine = Mockery::mock(TimesheetLine::class);
    $xeroLine->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'EarningsRateID' => 'earnings-rate-uuid',
        'NumberOfUnits' => [7.6],
        'UpdatedDateUTC' => null,
        default => null,
    });
    $xeroLine->shouldReceive('offsetExists')->andReturn(true);

    $xeroTimesheet = Mockery::mock(Timesheet::class);
    $xeroTimesheet->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'EmployeeID' => 'employee-uuid',
        'StartDate' => $startDate,
        'EndDate' => $endDate,
        'TimesheetLines' => new Collection([$xeroLine]),
        'TimesheetID' => 'timesheet-uuid-123',
        'Status' => XeroTimesheetStatusEnum::APPROVED,
        'Hours' => 38,
        'UpdatedDateUTC' => $updated,
        default => null,
    });
    $xeroTimesheet->shouldReceive('offsetExists')->andReturn(true);

    $data = XeroTimesheetData::fromXero($xeroTimesheet);

    expect($data)->toBeInstanceOf(XeroTimesheetData::class)
        ->and($data->EmployeeID)->toBe('employee-uuid')
        ->and($data->TimesheetID)->toBe('timesheet-uuid-123')
        ->and($data->Status)->toBe(XeroTimesheetStatusEnum::APPROVED)
        ->and($data->Hours)->toBe(38)
        ->and($data->UpdatedDateUTC)->toBe($updated)
        ->and($data->TimesheetLines)->toHaveCount(1)
        ->and($data->TimesheetLines->first())->toBeInstanceOf(XeroTimesheetLineData::class)
        ->and($data->TimesheetLines->first()->EarningsRateID)->toBe('earnings-rate-uuid');
});
