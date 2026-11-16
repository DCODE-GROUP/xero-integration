<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroTimesheetData;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroTimesheetLineData;
use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Enums\XeroTimesheetStatusEnum;
use LogicException;
use XeroPHP\Models\PayrollAU\Timesheet;

function sampleTimesheetPayload(array $overrides = []): array
{
    return array_merge([
        'timesheetID' => 'ts-1',
        'payrollCalendarID' => 'cal-1',
        'employeeID' => 'emp-1',
        'startDate' => '2026-11-02T00:00:00',
        'endDate' => '2026-11-08T00:00:00',
        'status' => 'Approved',
        'totalHours' => 14,
        'updatedDateUTC' => '2026-11-09T01:02:03',
        'timesheetLines' => [
            ['timesheetLineID' => 'l-1', 'date' => '2026-11-03T00:00:00', 'earningsRateID' => 'rate-1', 'trackingItemID' => null, 'numberOfUnits' => 8],
            ['timesheetLineID' => 'l-2', 'date' => '2026-11-05T00:00:00', 'earningsRateID' => 'rate-1', 'trackingItemID' => null, 'numberOfUnits' => 6],
        ],
    ], $overrides);
}

test('it is served by the Payroll AU 2.0 Timesheets endpoint', function () {
    $endpoint = XeroTimesheetData::getXeroEndpoint();

    expect($endpoint->api)->toBe(XeroApiEnum::PAYROLL_AU_V2)
        ->and($endpoint->api->baseUrl())->toBe('https://api.xero.com/payroll.xro/2.0')
        ->and($endpoint->resource)->toBe('Timesheets')
        ->and($endpoint->idKey)->toBe('timesheetID')
        ->and($endpoint->guidFilters)->toBe(['employeeId', 'payrollCalendarId'])
        ->and($endpoint->paginated)->toBeTrue()
        ->and(XeroTimesheetData::getXeroRelationship())->toBe(XeroRelationshipsEnum::TIMESHEET_AU);
});

test('it uses the Payroll AU 2.0 attributes', function () {
    $timesheet = new XeroTimesheetData('emp-1', 'cal-1', Carbon::parse('2026-11-02'), Carbon::parse('2026-11-08'), collect());

    expect(array_keys(get_object_vars($timesheet)))->toBe([
        'employeeID', 'payrollCalendarID', 'startDate', 'endDate', 'timesheetLines',
        'timesheetID', 'status', 'totalHours', 'updatedDateUTC',
    ]);
});

test('optional attributes default to null', function () {
    $timesheet = new XeroTimesheetData('emp-1', 'cal-1', Carbon::parse('2026-11-02'), Carbon::parse('2026-11-08'), collect());

    expect($timesheet->timesheetID)->toBeNull()
        ->and($timesheet->status)->toBeNull()
        ->and($timesheet->totalHours)->toBeNull()
        ->and($timesheet->updatedDateUTC)->toBeNull();
});

test('fromXeroArray hydrates a timesheet and its lines', function () {
    $timesheet = XeroTimesheetData::fromXeroArray(sampleTimesheetPayload());

    expect($timesheet->timesheetID)->toBe('ts-1')
        ->and($timesheet->payrollCalendarID)->toBe('cal-1')
        ->and($timesheet->employeeID)->toBe('emp-1')
        ->and($timesheet->startDate->toDateString())->toBe('2026-11-02')
        ->and($timesheet->endDate->toDateString())->toBe('2026-11-08')
        ->and($timesheet->status)->toBe(XeroTimesheetStatusEnum::APPROVED)
        ->and($timesheet->totalHours)->toBe(14.0)
        ->and($timesheet->updatedDateUTC->toIso8601String())->toBe('2026-11-09T01:02:03+00:00')
        ->and($timesheet->timesheetLines)->toHaveCount(2)
        ->and($timesheet->timesheetLines->first())->toBeInstanceOf(XeroTimesheetLineData::class)
        ->and($timesheet->timesheetLines->first()->numberOfUnits)->toBe(8.0);
});

test('fromXeroArray tolerates a timesheet without lines or status', function () {
    $timesheet = XeroTimesheetData::fromXeroArray(sampleTimesheetPayload(['timesheetLines' => null, 'status' => null, 'totalHours' => null, 'updatedDateUTC' => null]));

    expect($timesheet->timesheetLines)->toBeEmpty()
        ->and($timesheet->status)->toBeNull()
        ->and($timesheet->totalHours)->toBeNull();
});

test('toXeroPayload sends the writable 2.0 attributes only', function () {
    $timesheet = XeroTimesheetData::fromXeroArray(sampleTimesheetPayload());

    expect($timesheet->toXeroPayload())->toBe([
        'payrollCalendarID' => 'cal-1',
        'employeeID' => 'emp-1',
        'startDate' => '2026-11-02',
        'endDate' => '2026-11-08',
        'timesheetLines' => [
            ['date' => '2026-11-03', 'earningsRateID' => 'rate-1', 'numberOfUnits' => 8.0],
            ['date' => '2026-11-05', 'earningsRateID' => 'rate-1', 'numberOfUnits' => 6.0],
        ],
    ]);
});

test('every Payroll AU 2.0 status is understood', function (string $status, XeroTimesheetStatusEnum $expected) {
    expect(XeroTimesheetData::fromXeroArray(sampleTimesheetPayload(['status' => $status]))->status)->toBe($expected);
})->with([
    ['Draft', XeroTimesheetStatusEnum::DRAFT],
    ['Approved', XeroTimesheetStatusEnum::APPROVED],
    ['Completed', XeroTimesheetStatusEnum::COMPLETED],
]);

test('completed and processed timesheets are locked', function () {
    expect(XeroTimesheetStatusEnum::COMPLETED->isLocked())->toBeTrue()
        ->and(XeroTimesheetStatusEnum::PROCESSED->isLocked())->toBeTrue()
        ->and(XeroTimesheetStatusEnum::APPROVED->isLocked())->toBeFalse()
        ->and(XeroTimesheetStatusEnum::DRAFT->isLocked())->toBeFalse()
        ->and(XeroTimesheetStatusEnum::COMPLETED->getLabel())->toBe('Completed')
        ->and(XeroTimesheetStatusEnum::COMPLETED->toXeroAUValue())->toBe('Completed');
});

test('fromXero is not supported because xero-php has no 2.0 model', function () {
    XeroTimesheetData::fromXero(new Timesheet);
})->throws(LogicException::class, '2.0');
