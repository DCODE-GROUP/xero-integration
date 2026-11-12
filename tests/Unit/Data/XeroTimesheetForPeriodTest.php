<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroTimesheetData;
use Dcodegroup\XeroIntegration\Enums\XeroTimesheetStatusEnum;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Exceptions\XeroValidationException;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\FindXeroRecordRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\ListXeroRecordsRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\SaveXeroRecordRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\XeroActionBodyRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\XeroActionRequest;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;

const EMPLOYEE = '11111111-1111-1111-1111-111111111111';
const CALENDAR = '22222222-2222-2222-2222-222222222222';
const EXISTING_ID = '33333333-3333-3333-3333-333333333333';

function periodTimesheet(array $hoursByDate): XeroTimesheetData
{
    return XeroTimesheetData::forPeriod(EMPLOYEE, CALENDAR, Carbon::parse('2026-11-02'), Carbon::parse('2026-11-08'), 'rate-1', $hoursByDate);
}

/**
 * @param  array<int, array<string, mixed>>  $lines
 * @return array<string, mixed>
 */
function xeroTimesheet(string $status, array $lines = [], string $id = EXISTING_ID, string $employee = EMPLOYEE, string $start = '2026-11-02', string $end = '2026-11-08'): array
{
    return [
        'timesheetID' => $id,
        'payrollCalendarID' => CALENDAR,
        'employeeID' => $employee,
        'startDate' => "{$start}T00:00:00",
        'endDate' => "{$end}T00:00:00",
        'status' => $status,
        'totalHours' => collect($lines)->sum('numberOfUnits'),
        'updatedDateUTC' => '2026-11-09T01:02:03',
        'timesheetLines' => $lines,
    ];
}

function xeroLine(string $id, string $date, float $units, string $rate = 'rate-1'): array
{
    return ['timesheetLineID' => $id, 'date' => "{$date}T00:00:00", 'earningsRateID' => $rate, 'trackingItemID' => null, 'numberOfUnits' => $units];
}

/**
 * Fake Xero. `$existing` is what the list and find calls return; actions are logged in `$log`.
 *
 * @param  array<string, mixed>|null  $existing
 * @param  array<int, string>  $log
 * @param  (callable(string): bool)|null  $failWhen  Called with each logged action; true makes Xero answer 500.
 */
function fakeXero(?array $existing, array &$log, ?callable $failWhen = null): void
{
    $action = function (PendingRequest $request) use (&$log, $existing, $failWhen) {
        $path = str_replace('/payroll.xro/2.0/Timesheets/', '', $request->getUri()->getPath());
        $entry = $request->getMethod()->value.' '.preg_replace('#^[^/]+#', '{id}', $path).(empty($request->body()?->all()) ? '' : ' '.json_encode($request->body()->all()));
        $log[] = $entry;

        if ($failWhen !== null && $failWhen($entry)) {
            return MockResponse::make(['title' => 'Server error'], 500);
        }

        if (str_ends_with($path, '/Approve')) {
            return MockResponse::make(['timesheet' => xeroTimesheet('Approved', $existing['timesheetLines'] ?? [xeroLine('l', '2026-11-03', 8)], id: explode('/', $path)[0])]);
        }

        return MockResponse::make(['timesheet' => $existing]);
    };

    fakeSaloon([
        ListXeroRecordsRequest::class => function (PendingRequest $request) use ($existing, &$log) {
            $log[] = 'LIST '.json_encode($request->query()->all());

            return MockResponse::make(['pagination' => ['page' => 1, 'pageCount' => 1], 'timesheets' => $existing === null ? [] : [$existing]]);
        },
        FindXeroRecordRequest::class => function () use ($existing, &$log) {
            $log[] = 'FIND';

            return $existing === null
                ? MockResponse::make(['title' => 'Not found'], 404)
                : MockResponse::make(['timesheet' => $existing]);
        },
        SaveXeroRecordRequest::class => function (PendingRequest $request) use (&$log) {
            $log[] = 'CREATE '.json_encode($request->body()->all());

            return MockResponse::make(['timesheet' => xeroTimesheet('Draft', array_map(
                fn (array $line): array => xeroLine('new-'.$line['date'], $line['date'], $line['numberOfUnits']),
                $request->body()->all()['timesheetLines'],
            ), id: 'ts-new')]);
        },
        XeroActionRequest::class => $action,
        XeroActionBodyRequest::class => $action,
    ]);
}

beforeEach(function () {
    XeroToken::factory()->create(['current_tenant_id' => 'tenant-1']);
});

test('forPeriod builds one line per day with hours, in date order', function () {
    $timesheet = periodTimesheet(['2026-11-05' => 6, '2026-11-03' => 7.5]);

    expect($timesheet->employeeID)->toBe(EMPLOYEE)
        ->and($timesheet->payrollCalendarID)->toBe(CALENDAR)
        ->and($timesheet->startDate->toDateString())->toBe('2026-11-02')
        ->and($timesheet->endDate->toDateString())->toBe('2026-11-08')
        ->and($timesheet->timesheetLines->map(fn ($line) => [$line->date->toDateString(), $line->earningsRateID, $line->numberOfUnits])->all())
        ->toBe([['2026-11-03', 'rate-1', 7.5], ['2026-11-05', 'rate-1', 6.0]]);
});

test('forPeriod rounds hours to 2 decimals and totals hours given for the same day', function () {
    $timesheet = periodTimesheet(['2026-11-03 08:00' => 1.005, '2026-11-03 13:00' => 2.1234]);

    expect($timesheet->timesheetLines)->toHaveCount(1)
        ->and($timesheet->timesheetLines->first()->numberOfUnits)->toBe(3.13);
});

test('forPeriod skips days with no hours', function () {
    $timesheet = periodTimesheet(['2026-11-03' => 0, '2026-11-04' => 4]);

    expect($timesheet->timesheetLines->pluck('numberOfUnits')->all())->toBe([4.0]);
});

test('forPeriod rejects dates outside the period', function (string $date) {
    periodTimesheet([$date => 8]);
})->with(['2026-11-01', '2026-11-09'])->throws(XeroValidationException::class, 'outside the pay period');

test('forPeriod rejects an empty set of hours', function () {
    periodTimesheet([]);
})->throws(XeroValidationException::class, 'at least one day');

test('forPeriod rejects hours that are all zero', function () {
    periodTimesheet(['2026-11-03' => 0]);
})->throws(XeroValidationException::class, 'above zero');

test('forPeriod rejects a period that ends before it starts', function () {
    XeroTimesheetData::forPeriod(EMPLOYEE, CALENDAR, Carbon::parse('2026-11-08'), Carbon::parse('2026-11-02'), 'rate-1', ['2026-11-03' => 8]);
})->throws(XeroValidationException::class, 'ends before it starts');

test('a new period is created in one call and adopts the saved values', function () {
    $log = [];
    fakeXero(null, $log);
    $timesheet = periodTimesheet(['2026-11-03' => 7.5]);

    $timesheet->sendToXero();

    expect($timesheet->timesheetID)->toBe('ts-new')
        ->and($timesheet->status)->toBe(XeroTimesheetStatusEnum::DRAFT)
        ->and($timesheet->totalHours)->toBe(7.5)
        ->and($log)->toHaveCount(2)
        ->and($log[0])->toStartWith('LIST ')
        ->and($log[0])->toContain('"filter":"employeeId==11111111-1111-1111-1111-111111111111,payrollCalendarId==22222222-2222-2222-2222-222222222222"')
        ->and($log[0])->toContain('"startDate":"2026-11-02"')
        ->and($log[1])->toBe('CREATE {"payrollCalendarID":"'.CALENDAR.'","employeeID":"'.EMPLOYEE.'","startDate":"2026-11-02","endDate":"2026-11-08","timesheetLines":[{"date":"2026-11-03","earningsRateID":"rate-1","numberOfUnits":7.5}]}');
});

test('a new period with an explicit Approved status is approved after creation', function () {
    $log = [];
    fakeXero(null, $log);
    $timesheet = periodTimesheet(['2026-11-03' => 7.5]);
    $timesheet->status = XeroTimesheetStatusEnum::APPROVED;

    $timesheet->sendToXero();

    expect($log)->toContain('POST {id}/Approve')
        ->and($timesheet->status)->toBe(XeroTimesheetStatusEnum::APPROVED);
});

test('re-sending a draft period reconciles lines without reverting or approving', function () {
    $log = [];
    fakeXero(xeroTimesheet('Draft', [
        xeroLine('l-keep', '2026-11-03', 8),
        xeroLine('l-change', '2026-11-04', 2),
        xeroLine('l-remove', '2026-11-06', 5),
        xeroLine('l-duplicate', '2026-11-03', 1),
    ]), $log);

    $timesheet = periodTimesheet(['2026-11-03' => 8, '2026-11-04' => 3, '2026-11-05' => 6]);
    $timesheet->sendToXero();

    expect($timesheet->timesheetID)->toBe(EXISTING_ID)
        ->and($log)->not->toContain('POST {id}/RevertToDraft')
        ->and($log)->not->toContain('POST {id}/Approve')
        ->and($log)->toContain('PUT {id}/Lines/l-change {"date":"2026-11-04","earningsRateID":"rate-1","numberOfUnits":3}')
        ->and($log)->toContain('DELETE {id}/Lines/l-remove')
        ->and($log)->toContain('DELETE {id}/Lines/l-duplicate')
        ->and($log)->toContain('POST {id}/Lines {"date":"2026-11-05","earningsRateID":"rate-1","numberOfUnits":6}')
        ->and(collect($log)->filter(fn ($entry) => str_starts_with($entry, 'PUT '))->count())->toBe(1)
        ->and(collect($log)->filter(fn ($entry) => str_starts_with($entry, 'CREATE'))->count())->toBe(0);
});

test('re-sending an approved period reverts it and leaves it in draft', function () {
    $log = [];
    fakeXero(xeroTimesheet('Approved', [xeroLine('l-1', '2026-11-03', 8)]), $log);

    periodTimesheet(['2026-11-03' => 5])->sendToXero();

    $actions = collect($log)->filter(fn ($entry) => str_starts_with($entry, 'POST') || str_starts_with($entry, 'PUT') || str_starts_with($entry, 'DELETE'))->values()->all();

    expect($actions)->toBe([
        'POST {id}/RevertToDraft',
        'PUT {id}/Lines/l-1 {"date":"2026-11-03","earningsRateID":"rate-1","numberOfUnits":5}',
    ]);
});

test('re-sending an approved period with an explicit Approved status approves it again', function () {
    $log = [];
    fakeXero(xeroTimesheet('Approved', [xeroLine('l-1', '2026-11-03', 8)]), $log);

    $timesheet = periodTimesheet(['2026-11-03' => 5]);
    $timesheet->status = XeroTimesheetStatusEnum::APPROVED;
    $timesheet->sendToXero();

    $actions = collect($log)->filter(fn ($entry) => str_starts_with($entry, 'POST') || str_starts_with($entry, 'PUT') || str_starts_with($entry, 'DELETE'))->map(fn ($entry) => explode(' ', $entry, 3)[0].' '.explode(' ', $entry, 3)[1])->values()->all();

    expect($actions)->toBe(['POST {id}/RevertToDraft', 'PUT {id}/Lines/l-1', 'POST {id}/Approve'])
        ->and($timesheet->status)->toBe(XeroTimesheetStatusEnum::APPROVED);
});

test('an explicit Draft status leaves an approved timesheet in draft', function () {
    $log = [];
    fakeXero(xeroTimesheet('Approved', [xeroLine('l-1', '2026-11-03', 8)]), $log);

    $timesheet = periodTimesheet(['2026-11-03' => 8]);
    $timesheet->status = XeroTimesheetStatusEnum::DRAFT;
    $timesheet->sendToXero();

    expect($log)->toContain('POST {id}/RevertToDraft')
        ->and($log)->not->toContain('POST {id}/Approve');
});

test('an unchanged period makes no line changes', function () {
    $log = [];
    fakeXero(xeroTimesheet('Draft', [xeroLine('l-1', '2026-11-03', 8)]), $log);

    periodTimesheet(['2026-11-03' => 8])->sendToXero();

    expect(collect($log)->filter(fn ($entry) => str_starts_with($entry, 'PUT') || str_starts_with($entry, 'DELETE') || str_starts_with($entry, 'POST') || str_starts_with($entry, 'CREATE')))->toBeEmpty();
});

test('lines on another earnings rate are removed', function () {
    $log = [];
    fakeXero(xeroTimesheet('Draft', [xeroLine('l-old', '2026-11-03', 8, 'rate-old')]), $log);

    periodTimesheet(['2026-11-03' => 8])->sendToXero();

    expect($log)->toContain('DELETE {id}/Lines/l-old')
        ->and($log)->toContain('POST {id}/Lines {"date":"2026-11-03","earningsRateID":"rate-1","numberOfUnits":8}');
});

test('sending a completed period throws a readable exception and changes nothing', function () {
    $log = [];
    fakeXero(xeroTimesheet('Completed', [xeroLine('l-1', '2026-11-03', 8)]), $log);

    expect(fn () => periodTimesheet(['2026-11-03' => 5])->sendToXero())
        ->toThrow(XeroValidationException::class, 'is already Completed and can no longer be changed');

    expect(collect($log)->filter(fn ($entry) => ! str_starts_with($entry, 'LIST') && ! str_starts_with($entry, 'FIND')))->toBeEmpty();
});

test('a timesheet for a different period or employee is not adopted', function () {
    $log = [];
    fakeXero(xeroTimesheet('Draft', [xeroLine('l-1', '2026-11-10', 8)], start: '2026-11-09', end: '2026-11-15'), $log);
    periodTimesheet(['2026-11-03' => 8])->sendToXero();

    expect(collect($log)->contains(fn ($entry) => str_starts_with($entry, 'CREATE')))->toBeTrue();

    $log = [];
    fakeXero(xeroTimesheet('Draft', [xeroLine('l-1', '2026-11-03', 8)], employee: '99999999-9999-9999-9999-999999999999'), $log);
    periodTimesheet(['2026-11-03' => 8])->sendToXero();

    expect(collect($log)->contains(fn ($entry) => str_starts_with($entry, 'CREATE')))->toBeTrue();
});

test('a timesheet that already has an id is looked up directly', function () {
    $log = [];
    fakeXero(xeroTimesheet('Draft', [xeroLine('l-1', '2026-11-03', 8)]), $log);

    $timesheet = periodTimesheet(['2026-11-03' => 8]);
    $timesheet->timesheetID = EXISTING_ID;
    $timesheet->sendToXero();

    expect(collect($log)->contains(fn ($entry) => str_starts_with($entry, 'LIST')))->toBeFalse()
        ->and($log[0])->toBe('FIND');
});

test('lines are created and updated before any are deleted', function () {
    $log = [];
    fakeXero(xeroTimesheet('Draft', [xeroLine('l-remove', '2026-11-06', 5), xeroLine('l-change', '2026-11-04', 2)]), $log);

    periodTimesheet(['2026-11-04' => 3, '2026-11-05' => 6])->sendToXero();

    $actions = collect($log)->filter(fn ($entry) => preg_match('/^(PUT|POST|DELETE) /', $entry))->map(fn ($entry) => explode(' ', $entry)[0])->values()->all();

    expect($actions)->toBe(['PUT', 'POST', 'DELETE']);
});

test('an unchanged re-send leaves an approved timesheet approved and sends nothing', function () {
    $log = [];
    fakeXero(xeroTimesheet('Approved', [xeroLine('l-1', '2026-11-03', 8)]), $log);

    $timesheet = periodTimesheet(['2026-11-03' => 8]);
    $timesheet->sendToXero();

    expect(collect($log)->contains(fn ($entry) => preg_match('/^(PUT|POST|DELETE|CREATE)/', $entry)))->toBeFalse()
        ->and($timesheet->status)->toBe(XeroTimesheetStatusEnum::APPROVED)
        ->and($timesheet->timesheetID)->toBe(EXISTING_ID);
});

test('an unchanged re-send with an explicit Approved status approves a draft timesheet', function () {
    $log = [];
    fakeXero(xeroTimesheet('Draft', [xeroLine('l-1', '2026-11-03', 8)]), $log);

    $timesheet = periodTimesheet(['2026-11-03' => 8]);
    $timesheet->status = XeroTimesheetStatusEnum::APPROVED;
    $timesheet->sendToXero();

    expect(collect($log)->filter(fn ($entry) => preg_match('/^(PUT|POST|DELETE|CREATE)/', $entry))->values()->all())->toBe(['POST {id}/Approve']);
});

test('an unchanged re-send with an explicit Approved status leaves an approved timesheet alone', function () {
    $log = [];
    fakeXero(xeroTimesheet('Approved', [xeroLine('l-1', '2026-11-03', 8)]), $log);

    $timesheet = periodTimesheet(['2026-11-03' => 8]);
    $timesheet->status = XeroTimesheetStatusEnum::APPROVED;
    $timesheet->sendToXero();

    expect(collect($log)->contains(fn ($entry) => preg_match('/^(PUT|POST|DELETE|CREATE)/', $entry)))->toBeFalse();
});

test('a failure part way through the line changes leaves the timesheet in draft and says so', function () {
    $log = [];
    fakeXero(xeroTimesheet('Approved', [xeroLine('l-1', '2026-11-03', 8)]), $log, fn (string $entry): bool => str_starts_with($entry, 'PUT '));

    try {
        periodTimesheet(['2026-11-03' => 5])->sendToXero();
    } catch (XeroIntegrationException $exception) {
        expect($exception->getMessage())->toContain('reverted to Draft')
            ->toContain('send it again with the status Approved')
            ->not->toContain('status 500')
            ->and($exception->getPrevious())->toBeInstanceOf(XeroIntegrationException::class)
            ->and($log)->not->toContain('POST {id}/Approve');

        return;
    }

    test()->fail('Expected an exception.');
});

test('a failed approval after the lines were updated is reported as left in draft', function () {
    $log = [];
    fakeXero(xeroTimesheet('Approved', [xeroLine('l-1', '2026-11-03', 8)]), $log, fn (string $entry): bool => str_ends_with($entry, '/Approve'));

    $timesheet = periodTimesheet(['2026-11-03' => 5]);
    $timesheet->status = XeroTimesheetStatusEnum::APPROVED;

    expect(fn () => $timesheet->sendToXero())
        ->toThrow(XeroIntegrationException::class, 'may have been reverted to Draft');
});

test('a failed revert is reported as possibly draft, without repeating the failure details', function () {
    $log = [];
    fakeXero(xeroTimesheet('Approved', [xeroLine('l-1', '2026-11-03', 8)]), $log, fn (string $entry): bool => str_ends_with($entry, '/RevertToDraft'));

    try {
        periodTimesheet(['2026-11-03' => 5])->sendToXero();
    } catch (XeroIntegrationException $exception) {
        expect($exception->getMessage())->toContain('may have been reverted to Draft')->not->toContain('status 500')
            ->and($exception->getPrevious()->getMessage())->toContain('status 500');
    }

    expect(collect($log)->contains(fn ($entry) => str_starts_with($entry, 'PUT') || str_starts_with($entry, 'DELETE')))->toBeFalse();
});

test('a given timesheet id that belongs to another employee or period is refused', function () {
    $log = [];
    fakeXero(xeroTimesheet('Draft', [xeroLine('l-1', '2026-11-03', 8)], employee: '99999999-9999-9999-9999-999999999999'), $log);

    $timesheet = periodTimesheet(['2026-11-03' => 8]);
    $timesheet->timesheetID = EXISTING_ID;

    expect(fn () => $timesheet->sendToXero())->toThrow(XeroValidationException::class, 'different employee or pay period');

    expect(collect($log)->contains(fn ($entry) => preg_match('/^(PUT|POST|DELETE|CREATE)/', $entry)))->toBeFalse();
});

test('the send lock is released afterwards, including after a failure', function () {
    $key = 'xero-integration:timesheet:'.EMPLOYEE.':2026-11-02:2026-11-08';
    $log = [];
    fakeXero(null, $log);

    periodTimesheet(['2026-11-03' => 8])->sendToXero();

    $lock = Cache::lock($key, 10);
    expect($lock->get())->toBeTrue();
    $lock->release();

    $log = [];
    fakeXero(xeroTimesheet('Completed'), $log);
    expect(fn () => periodTimesheet(['2026-11-03' => 8])->sendToXero())->toThrow(XeroValidationException::class);

    $lock = Cache::lock($key, 10);
    expect($lock->get())->toBeTrue();
    $lock->release();
});

test('a timesheet id must be a guid', function () {
    $log = [];
    fakeXero(null, $log);

    $timesheet = periodTimesheet(['2026-11-03' => 8]);
    $timesheet->timesheetID = '../Employees';

    expect(fn () => $timesheet->sendToXero())->toThrow(XeroValidationException::class, 'not a valid GUID');
});

test('employee and calendar ids are matched without regard to case', function () {
    $log = [];
    fakeXero(xeroTimesheet('Draft', [xeroLine('l-1', '2026-11-03', 8)], employee: strtoupper(EMPLOYEE)), $log);

    periodTimesheet(['2026-11-03' => 8])->sendToXero();

    expect(collect($log)->contains(fn ($entry) => str_starts_with($entry, 'CREATE')))->toBeFalse();
});

test('a timesheet on another payroll calendar is not adopted', function () {
    $log = [];
    $other = xeroTimesheet('Draft', [xeroLine('l-1', '2026-11-03', 8)]);
    $other['payrollCalendarID'] = '99999999-9999-9999-9999-999999999999';
    fakeXero($other, $log);

    periodTimesheet(['2026-11-03' => 8])->sendToXero();

    expect(collect($log)->contains(fn ($entry) => str_starts_with($entry, 'CREATE')))->toBeTrue();
});
