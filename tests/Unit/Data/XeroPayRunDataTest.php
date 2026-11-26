<?php

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroPayrollCalendarData;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroPayRunData;
use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\ListXeroRecordsRequest;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockResponse;
use XeroPHP\Models\PayrollAU\PayRun;

beforeEach(function () {
    if (! config()->has('data')) {
        config(['data' => require __DIR__.'/../../../vendor/spatie/laravel-data/config/data.php']);
    }
});

test('pay runs use the paginated Payroll AU endpoint and model', function () {
    $endpoint = XeroPayRunData::getXeroEndpoint();

    expect($endpoint->api)->toBe(XeroApiEnum::PAYROLL_AU)
        ->and($endpoint->resource)->toBe('PayRuns')
        ->and($endpoint->idKey)->toBe('PayRunID')
        ->and($endpoint->paginated)->toBeTrue()
        ->and(XeroPayRunData::getXeroRelationship())->toBe(XeroRelationshipsEnum::PAY_RUN_AU)
        ->and(XeroRelationshipsEnum::PAY_RUN_AU->getModelClass())->toBe(PayRun::class);
});

test('maps pay run dates totals and payslips', function () {
    $data = XeroPayRunData::fromXeroArray([
        'PayRunID' => 'run-1', 'PayrollCalendarID' => '11111111-1111-4111-8111-111111111111', 'PayRunStatus' => 'POSTED',
        'PayRunPeriodStartDate' => '2026-11-02T00:00:00',
        'PayRunPeriodEndDate' => new DateTimeImmutable('2026-11-08'),
        'PaymentDate' => '2026-11-12', 'UpdatedDateUTC' => '2026-11-12T04:00:00',
        'Wages' => '1000.50', 'Deductions' => 0, 'Tax' => '200', 'Super' => '120.06',
        'Reimbursement' => 10, 'NetPay' => '810.50', 'PayslipMessage' => 'Weekly pay',
        'Payslips' => [['PayslipID' => 'payslip-1']],
    ]);

    expect($data->PayRunStatus)->toBe('POSTED')
        ->and($data->PayRunPeriodStartDate->toDateString())->toBe('2026-11-02')
        ->and($data->PayRunPeriodEndDate->toDateString())->toBe('2026-11-08')
        ->and($data->PaymentDate->toDateString())->toBe('2026-11-12')
        ->and($data->UpdatedDateUTC->utc()->format('Y-m-d H:i:s'))->toBe('2026-11-12 04:00:00')
        ->and($data->Wages)->toBe(1000.5)
        ->and($data->Deductions)->toBe(0.0)
        ->and($data->Tax)->toBe(200.0)
        ->and($data->Super)->toBe(120.06)
        ->and($data->Reimbursement)->toBe(10.0)
        ->and($data->NetPay)->toBe(810.5)
        ->and($data->PayslipMessage)->toBe('Weekly pay')
        ->and($data->Payslips)->toBe([['PayslipID' => 'payslip-1']]);
});

test('maps a summary with missing optional fields', function () {
    $data = XeroPayRunData::fromXeroArray(['PayRunID' => 'run-1', 'PayrollCalendarID' => '11111111-1111-4111-8111-111111111111', 'PaymentDate' => null]);

    expect($data->PaymentDate)->toBeNull()
        ->and($data->PayRunPeriodStartDate)->toBeNull()
        ->and($data->Wages)->toBeNull()
        ->and($data->Payslips)->toBeNull();
});

test('pay runs are read only', function () {
    (new XeroPayRunData('run-1', '11111111-1111-4111-8111-111111111111'))->toXeroArray();
})->throws(LogicException::class, 'read-only');

test('pay runs use direct API mapping', function () {
    XeroPayRunData::fromXero(new PayRun);
})->throws(LogicException::class, 'fromXeroArray');

test('calendar pay runs are filtered and read across pages', function () {
    XeroToken::factory()->create(['current_tenant_id' => 'tenant-1']);
    $mock = fakeSaloon([
        MockResponse::make(['PayRuns' => [['PayRunID' => 'run-1', 'PayrollCalendarID' => '11111111-1111-4111-8111-111111111111']]]),
        MockResponse::make(['PayRuns' => [['PayRunID' => 'run-2', 'PayrollCalendarID' => '11111111-1111-4111-8111-111111111111']]]),
        MockResponse::make(['PayRuns' => []]),
    ]);

    $calendar = new XeroPayrollCalendarData('11111111-1111-4111-8111-111111111111', 'Weekly', 'WEEKLY');

    expect($calendar->payRuns()->pluck('PayRunID')->all())->toBe(['run-1', 'run-2']);
    $mock->assertSent(fn (ListXeroRecordsRequest $request): bool => $request->query()->get('where') === 'PayrollCalendarID=Guid("11111111-1111-4111-8111-111111111111")'
        && $request->query()->get('page') === 2);
    $mock->assertSentCount(3);
});

test('find maps normalised dates and returns null for a missing pay run', function () {
    XeroToken::factory()->create(['current_tenant_id' => 'tenant-1']);
    $milliseconds = Carbon::parse('2026-11-12', 'UTC')->getTimestampMs();
    fakeSaloon([
        MockResponse::make(['PayRuns' => [['PayRunID' => 'run-1', 'PayrollCalendarID' => '11111111-1111-4111-8111-111111111111', 'PaymentDate' => "/Date({$milliseconds}+0000)/"]]]),
        MockResponse::make(['Message' => 'not found'], 404),
    ]);

    expect(XeroPayRunData::find('run-1')->PaymentDate->toDateString())->toBe('2026-11-12')
        ->and(XeroPayRunData::find('missing'))->toBeNull();
});

test('pay run resolves its calendar through the cached list', function () {
    Cache::flush();
    XeroToken::factory()->create(['current_tenant_id' => 'tenant-1']);
    $mock = fakeSaloon([
        ListXeroRecordsRequest::class => MockResponse::make(['PayrollCalendars' => [
            ['PayrollCalendarID' => '11111111-1111-4111-8111-111111111111', 'Name' => 'Weekly', 'CalendarType' => 'WEEKLY'],
        ]]),
    ]);

    expect((new XeroPayRunData('run-1', '11111111-1111-4111-8111-111111111111'))->payrollCalendar()?->Name)->toBe('Weekly')
        ->and((new XeroPayRunData('run-2', 'missing'))->payrollCalendar())->toBeNull();
    $mock->assertSentCount(1);
});
