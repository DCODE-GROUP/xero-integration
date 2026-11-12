<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroEmployeeData;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroPayrollCalendarData;
use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Exceptions\XeroValidationException;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\FindXeroRecordRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\ListXeroRecordsRequest;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockResponse;
use XeroPHP\Models\PayrollAU\PayrollCalendar;

function calendar(string $type, ?string $start = '2026-11-02', ?string $reference = null): XeroPayrollCalendarData
{
    return new XeroPayrollCalendarData(
        PayrollCalendarID: 'calendar-uuid',
        Name: 'Test calendar',
        CalendarType: $type,
        StartDate: $start ? Carbon::parse($start) : null,
        ReferenceDate: $reference ? Carbon::parse($reference) : null,
    );
}

function period(XeroPayrollCalendarData $calendar, string $date): array
{
    $period = $calendar->payPeriodFor(Carbon::parse($date));

    return [$period['start']->toDateString(), $period['end']->toDateString()];
}

test('maps to the PayrollCalendars resource', function () {
    expect(XeroRelationshipsEnum::PAYROLL_CALENDAR_AU->getModelClass())->toBe(PayrollCalendar::class)
        ->and(XeroPayrollCalendarData::getXeroRelationship())->toBe(XeroRelationshipsEnum::PAYROLL_CALENDAR_AU);
});

test('fromXero is not supported because calendars are read through the direct API', function () {
    XeroPayrollCalendarData::fromXero(new PayrollCalendar);
})->throws(\LogicException::class, 'fromXeroArray');

test('fromXeroArray maps a calendar', function () {
    $data = XeroPayrollCalendarData::fromXeroArray([
        'PayrollCalendarID' => 'calendar-uuid',
        'Name' => 'Weekly',
        'CalendarType' => 'WEEKLY',
        'StartDate' => '2026-11-02T00:00:00',
        'PaymentDate' => '2026-11-12T00:00:00',
    ]);

    expect($data->PayrollCalendarID)->toBe('calendar-uuid')
        ->and($data->CalendarType)->toBe('WEEKLY')
        ->and($data->StartDate->toDateString())->toBe('2026-11-02')
        ->and($data->PaymentDate->toDateString())->toBe('2026-11-12')
        ->and($data->ReferenceDate)->toBeNull();
});

test('is read-only', function () {
    calendar('WEEKLY')->toXeroArray();
})->throws(\LogicException::class);

test('weekly periods step from the anchor in both directions', function (string $date, string $start, string $end) {
    expect(period(calendar('WEEKLY'), $date))->toBe([$start, $end]);
})->with([
    'anchor day' => ['2026-11-02', '2026-11-02', '2026-11-08'],
    'end of anchor period' => ['2026-11-08', '2026-11-02', '2026-11-08'],
    'next period' => ['2026-11-09', '2026-11-09', '2026-11-15'],
    'previous period' => ['2026-11-01', '2026-10-26', '2026-11-01'],
    'far in the past' => ['2025-01-01', '2024-12-30', '2025-01-05'],
]);

test('fortnightly periods', function (string $date, string $start, string $end) {
    expect(period(calendar('FORTNIGHTLY'), $date))->toBe([$start, $end]);
})->with([
    ['2026-11-15', '2026-11-02', '2026-11-15'],
    ['2026-11-16', '2026-11-16', '2026-11-29'],
    ['2026-10-30', '2026-10-19', '2026-11-01'],
]);

test('four weekly periods', function () {
    expect(period(calendar('FOURWEEKLY'), '2026-11-30'))->toBe(['2026-11-30', '2026-12-27']);
});

test('monthly periods anchored on the first', function (string $date, string $start, string $end) {
    expect(period(calendar('MONTHLY', '2026-11-01'), $date))->toBe([$start, $end]);
})->with([
    ['2026-11-15', '2026-11-01', '2026-11-30'],
    ['2026-02-10', '2026-02-01', '2026-02-28'],
    ['2027-01-31', '2027-01-01', '2027-01-31'],
]);

test('monthly periods anchored mid month', function (string $date, string $start, string $end) {
    expect(period(calendar('MONTHLY', '2026-11-15'), $date))->toBe([$start, $end]);
})->with([
    ['2026-11-20', '2026-11-15', '2026-12-14'],
    ['2026-11-10', '2026-10-15', '2026-11-14'],
    ['2026-12-14', '2026-11-15', '2026-12-14'],
]);

test('quarterly periods', function () {
    expect(period(calendar('QUARTERLY', '2026-07-01'), '2026-11-12'))->toBe(['2026-10-01', '2026-12-31']);
});

test('reference date takes priority over start date', function () {
    $calendar = calendar('WEEKLY', '2026-11-02', '2026-10-28');

    expect(period($calendar, '2026-11-02'))->toBe(['2026-10-28', '2026-11-03']);
});

test('twice monthly calendars throw', function () {
    period(calendar('TWICEMONTHLY'), '2026-11-12');
})->throws(XeroValidationException::class, 'cannot be resolved');

test('unknown calendar types throw', function () {
    period(calendar('SOMETHINGELSE'), '2026-11-12');
})->throws(XeroValidationException::class, 'cannot be resolved');

test('monthly calendars anchored after day 28 throw', function () {
    period(calendar('MONTHLY', '2026-10-31'), '2026-11-12');
})->throws(XeroValidationException::class, 'day 31');

test('calendars without an anchor date throw', function () {
    period(calendar('WEEKLY', null), '2026-11-12');
})->throws(XeroValidationException::class, 'no start or reference date');

function employeeWithCalendar(?string $payrollCalendarId): XeroEmployeeData
{
    $employee = (new \ReflectionClass(XeroEmployeeData::class))->newInstanceWithoutConstructor();
    $employee->PayrollCalendarID = $payrollCalendarId;

    return $employee;
}

test('employee payrollCalendar is null without a calendar id', function () {
    expect(employeeWithCalendar(null)->payrollCalendar())->toBeNull();
});

test('it is served by the Payroll AU 1.0 PayrollCalendars endpoint', function () {
    $endpoint = XeroPayrollCalendarData::getXeroEndpoint();

    expect($endpoint->api)->toBe(XeroApiEnum::PAYROLL_AU)
        ->and($endpoint->resource)->toBe('PayrollCalendars')
        ->and($endpoint->idKey)->toBe('PayrollCalendarID');
});

test('list and find read calendars through the gateway with normalised dates', function () {
    XeroToken::factory()->create(['current_tenant_id' => 'tenant-1']);
    $ms = Carbon::parse('2026-11-02', 'UTC')->getTimestampMs();
    $record = ['PayrollCalendarID' => 'calendar-uuid', 'Name' => 'Weekly', 'CalendarType' => 'WEEKLY', 'StartDate' => "/Date({$ms}+0000)/"];

    fakeSaloon([
        ListXeroRecordsRequest::class => MockResponse::make(['PayrollCalendars' => [$record]]),
        FindXeroRecordRequest::class => MockResponse::make(['PayrollCalendars' => [$record]]),
    ]);

    expect(XeroPayrollCalendarData::list()->first()->StartDate->toDateString())->toBe('2026-11-02')
        ->and(XeroPayrollCalendarData::find('calendar-uuid')->Name)->toBe('Weekly');
});

test('find returns null for an unknown calendar', function () {
    XeroToken::factory()->create(['current_tenant_id' => 'tenant-1']);
    fakeSaloon([FindXeroRecordRequest::class => MockResponse::make(['Message' => 'not found'], 404)]);

    expect(XeroPayrollCalendarData::find('missing'))->toBeNull();
});

test('employee payrollCalendar loads the assigned calendar from the cached list', function () {
    Cache::flush();
    XeroToken::factory()->create(['current_tenant_id' => 'tenant-a']);
    fakeSaloon([
        ListXeroRecordsRequest::class => MockResponse::make(['PayrollCalendars' => [
            ['PayrollCalendarID' => 'calendar-uuid', 'Name' => 'Weekly', 'CalendarType' => 'WEEKLY', 'StartDate' => '2026-11-02T00:00:00'],
        ]]),
    ]);

    expect(employeeWithCalendar('calendar-uuid')->payrollCalendar()?->PayrollCalendarID)->toBe('calendar-uuid')
        ->and(employeeWithCalendar('missing')->payrollCalendar())->toBeNull();
});
