<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroLeaveApplicationData;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroLeavePeriodData;
use Dcodegroup\XeroIntegration\Enums\XeroLeavePayoutTypeEnum;
use Illuminate\Support\Collection;
use Mockery;
use XeroPHP\Models\PayrollAU\LeaveApplication;
use XeroPHP\Models\PayrollAU\LeaveApplication\LeavePeriod;

test('can instantiate XeroLeaveApplicationData with required fields', function () {
    $startDate = Carbon::parse('2024-01-01');
    $endDate = Carbon::parse('2024-01-14');

    $data = new XeroLeaveApplicationData(
        EmployeeID: 'employee-uuid',
        LeaveTypeID: 'leave-type-uuid',
        Title: 'Annual Leave',
        StartDate: $startDate,
        EndDate: $endDate,
    );

    expect($data->EmployeeID)->toBe('employee-uuid')
        ->and($data->LeaveTypeID)->toBe('leave-type-uuid')
        ->and($data->Title)->toBe('Annual Leave')
        ->and($data->StartDate)->toBe($startDate)
        ->and($data->EndDate)->toBe($endDate);
});

test('optional fields default to null', function () {
    $data = new XeroLeaveApplicationData(
        EmployeeID: 'employee-uuid',
        LeaveTypeID: 'leave-type-uuid',
        Title: 'Annual Leave',
        StartDate: Carbon::parse('2024-01-01'),
        EndDate: Carbon::parse('2024-01-14'),
    );

    expect($data->LeavePeriods)->toBeNull()
        ->and($data->PayOutType)->toBeNull()
        ->and($data->Description)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroLeaveApplicationData(
        EmployeeID: 'employee-uuid',
        LeaveTypeID: 'leave-type-uuid',
        Title: 'Annual Leave',
        StartDate: Carbon::parse('2024-01-01'),
        EndDate: Carbon::parse('2024-01-14'),
    );

    expect($data->toXeroArray())->toHaveKeys([
        'EmployeeID', 'LeaveTypeID', 'Title', 'StartDate', 'EndDate',
        'LeavePeriods', 'PayOutType', 'Description',
    ]);
});

test('toXeroArray returns correct values', function () {
    $startDate = Carbon::parse('2024-01-01');
    $endDate = Carbon::parse('2024-01-14');
    $period = new XeroLeavePeriodData(
        NumberOfUnits: 7.6,
        PayPeriodEndDate: Carbon::parse('2024-01-14'),
    );

    $data = new XeroLeaveApplicationData(
        EmployeeID: 'employee-uuid',
        LeaveTypeID: 'leave-type-uuid',
        Title: 'Annual Leave',
        StartDate: $startDate,
        EndDate: $endDate,
        LeavePeriods: new Collection([$period]),
        PayOutType: XeroLeavePayoutTypeEnum::CASHED_OUT,
        Description: 'Cashing out leave',
    );

    $array = $data->toXeroArray();

    expect($array['EmployeeID'])->toBe('employee-uuid')
        ->and($array['LeaveTypeID'])->toBe('leave-type-uuid')
        ->and($array['Title'])->toBe('Annual Leave')
        ->and($array['StartDate'])->toBe($startDate)
        ->and($array['EndDate'])->toBe($endDate)
        ->and($array['LeavePeriods'])->toBe([$period->toXeroArray()])
        ->and($array['PayOutType'])->toBe('CASHED_OUT')
        ->and($array['Description'])->toBe('Cashing out leave');
});

test('toXeroArray maps PayOutType enum to Xero AU value', function ($type, $expected) {
    $data = new XeroLeaveApplicationData(
        EmployeeID: 'employee-uuid',
        LeaveTypeID: 'leave-type-uuid',
        Title: 'Annual Leave',
        StartDate: Carbon::parse('2024-01-01'),
        EndDate: Carbon::parse('2024-01-14'),
        PayOutType: $type,
    );

    expect($data->toXeroArray()['PayOutType'])->toBe($expected);
})->with([
    [XeroLeavePayoutTypeEnum::DEFAULT, 'DEFAULT'],
    [XeroLeavePayoutTypeEnum::CASHED_OUT, 'CASHED_OUT'],
]);

test('toXeroArray returns null PayOutType when not set', function () {
    $data = new XeroLeaveApplicationData(
        EmployeeID: 'employee-uuid',
        LeaveTypeID: 'leave-type-uuid',
        Title: 'Annual Leave',
        StartDate: Carbon::parse('2024-01-01'),
        EndDate: Carbon::parse('2024-01-14'),
    );

    expect($data->toXeroArray()['PayOutType'])->toBeNull();
});

test('toXeroArray returns null LeavePeriods when empty', function () {
    $data = new XeroLeaveApplicationData(
        EmployeeID: 'employee-uuid',
        LeaveTypeID: 'leave-type-uuid',
        Title: 'Annual Leave',
        StartDate: Carbon::parse('2024-01-01'),
        EndDate: Carbon::parse('2024-01-14'),
        LeavePeriods: new Collection,
    );

    expect($data->toXeroArray()['LeavePeriods'])->toBeNull();
});

test('fromXero maps xero model to XeroLeaveApplicationData', function () {
    $startDate = Carbon::parse('2024-01-01');
    $endDate = Carbon::parse('2024-01-14');

    $xeroPeriod = Mockery::mock(LeavePeriod::class);
    $xeroPeriod->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'NumberOfUnits' => 7.6,
        'PayPeriodEndDate' => $endDate,
        'PayPeriodStartDate' => $startDate,
        'LeavePeriodStatus' => null,
        default => null,
    });
    $xeroPeriod->shouldReceive('offsetExists')->andReturn(true);

    $xeroApplication = Mockery::mock(LeaveApplication::class);
    $xeroApplication->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'EmployeeID' => 'employee-uuid',
        'LeaveTypeID' => 'leave-type-uuid',
        'Title' => 'Annual Leave',
        'StartDate' => $startDate,
        'EndDate' => $endDate,
        'LeavePeriods' => new Collection([$xeroPeriod]),
        'PayOutType' => XeroLeavePayoutTypeEnum::DEFAULT,
        'Description' => 'Time off',
        default => null,
    });
    $xeroApplication->shouldReceive('offsetExists')->andReturn(true);

    $data = XeroLeaveApplicationData::fromXero($xeroApplication);

    expect($data)->toBeInstanceOf(XeroLeaveApplicationData::class)
        ->and($data->EmployeeID)->toBe('employee-uuid')
        ->and($data->LeaveTypeID)->toBe('leave-type-uuid')
        ->and($data->Title)->toBe('Annual Leave')
        ->and($data->PayOutType)->toBe(XeroLeavePayoutTypeEnum::DEFAULT)
        ->and($data->Description)->toBe('Time off')
        ->and($data->LeavePeriods)->toHaveCount(1)
        ->and($data->LeavePeriods->first())->toBeInstanceOf(XeroLeavePeriodData::class)
        ->and($data->LeavePeriods->first()->NumberOfUnits)->toBe(7.6);
});

test('fromXero returns null LeavePeriods when not present', function () {
    $xeroApplication = Mockery::mock(LeaveApplication::class);
    $xeroApplication->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'EmployeeID' => 'employee-uuid',
        'LeaveTypeID' => 'leave-type-uuid',
        'Title' => 'Annual Leave',
        'StartDate' => Carbon::parse('2024-01-01'),
        'EndDate' => Carbon::parse('2024-01-14'),
        default => null,
    });
    $xeroApplication->shouldReceive('offsetExists')->andReturnUsing(fn ($key) => in_array($key, ['EmployeeID', 'LeaveTypeID', 'Title', 'StartDate', 'EndDate'], true));

    $data = XeroLeaveApplicationData::fromXero($xeroApplication);

    expect($data->LeavePeriods)->toBeNull()
        ->and($data->PayOutType)->toBeNull()
        ->and($data->Description)->toBeNull();
});
