<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroTimesheetLineData;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Exceptions\XeroValidationException;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\XeroActionBodyRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\XeroActionRequest;
use Dcodegroup\XeroIntegration\Models\XeroRecord;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;

const LINE_SYNC_TIMESHEET = '33333333-3333-3333-3333-333333333333';

class LineSyncLocalModel extends Model
{
    protected $table = 'xero_tokens';

    public function xeroRecord(): MorphOne
    {
        return $this->morphOne(XeroRecord::class, 'recordable');
    }
}

function lineSyncModel(int $id = 7): LineSyncLocalModel
{
    $model = new LineSyncLocalModel;
    $model->forceFill(['id' => $id]);
    $model->exists = true;

    return $model;
}

function lineSyncLine(?string $id = null, float $units = 7.5): XeroTimesheetLineData
{
    return new XeroTimesheetLineData(Carbon::parse('2026-11-03'), 'rate-1', $units, timesheetLineID: $id);
}

/**
 * Fake Xero's line endpoints, logging each request as "METHOD path body".
 *
 * @param  array<int, string>  $log
 * @param  array<string, mixed>|null  $response
 */
function fakeLineXero(array &$log, ?array $response = null, int $status = 200): void
{
    $handler = function (PendingRequest $request) use (&$log, $response, $status) {
        $path = str_replace('/payroll.xro/2.0/Timesheets/', '', $request->getUri()->getPath());
        $log[] = $request->getMethod()->value.' '.$path.(empty($request->body()?->all()) ? '' : ' '.json_encode($request->body()->all()));

        return MockResponse::make($response ?? ['timesheetLine' => ['timesheetLineID' => 'line-new', 'date' => '2026-11-03T00:00:00', 'earningsRateID' => 'rate-1', 'numberOfUnits' => 7.5]], $status);
    };

    fakeSaloon([XeroActionRequest::class => $handler, XeroActionBodyRequest::class => $handler]);
}

beforeEach(function () {
    XeroToken::factory()->create(['current_tenant_id' => 'tenant-1']);
});

test('a line without an id is created on the timesheet and gets the Xero id', function () {
    $log = [];
    fakeLineXero($log);
    $line = lineSyncLine();

    $result = $line->sendToXero(LINE_SYNC_TIMESHEET);

    expect($result)->toBe($line)
        ->and($line->timesheetLineID)->toBe('line-new')
        ->and($log)->toBe(['POST '.LINE_SYNC_TIMESHEET.'/Lines {"date":"2026-11-03","earningsRateID":"rate-1","numberOfUnits":7.5}']);
});

test('a line with an id is updated at the line url', function () {
    $log = [];
    fakeLineXero($log, ['timesheetLine' => ['timesheetLineID' => 'line-1']]);
    $line = lineSyncLine('line-1', 3.0);

    $line->sendToXero(LINE_SYNC_TIMESHEET);

    expect($line->timesheetLineID)->toBe('line-1')
        ->and($log)->toBe(['PUT '.LINE_SYNC_TIMESHEET.'/Lines/line-1 {"date":"2026-11-03","earningsRateID":"rate-1","numberOfUnits":3}']);
});

test('a bare line response is accepted', function () {
    $log = [];
    fakeLineXero($log, ['timesheetLineID' => 'line-bare']);
    $line = lineSyncLine();

    $line->sendToXero(LINE_SYNC_TIMESHEET);

    expect($line->timesheetLineID)->toBe('line-bare');
});

test('a response without a line id is an integration error', function () {
    $log = [];
    fakeLineXero($log, ['timesheetLine' => []]);

    lineSyncLine()->sendToXero(LINE_SYNC_TIMESHEET);
})->throws(XeroIntegrationException::class, 'did not return');

test('a line is deleted by timesheet id and line id', function () {
    $log = [];
    fakeLineXero($log);

    lineSyncLine('line-1')->deleteFromXero(LINE_SYNC_TIMESHEET);

    expect($log)->toBe(['DELETE '.LINE_SYNC_TIMESHEET.'/Lines/line-1']);
});

test('a malformed timesheet id is rejected before any request', function (string $method) {
    $log = [];
    fakeLineXero($log);
    $line = lineSyncLine('line-1');

    expect(fn () => $line->{$method}('../Employees'))->toThrow(XeroValidationException::class, 'not a valid GUID')
        ->and($log)->toBeEmpty();
})->with(['sendToXero', 'deleteFromXero']);

test('a delete without a line id is rejected before any request', function () {
    $log = [];
    fakeLineXero($log);

    expect(fn () => lineSyncLine()->deleteFromXero(LINE_SYNC_TIMESHEET))->toThrow(XeroValidationException::class, 'timesheetLineID')
        ->and($log)->toBeEmpty();
});

test('a Xero rejection surfaces as the package exception and makes no revert or approve call', function () {
    $log = [];
    fakeLineXero($log, ['title' => 'Timesheet is locked'], 400);

    expect(fn () => lineSyncLine('line-1')->sendToXero(LINE_SYNC_TIMESHEET))->toThrow(XeroValidationException::class)
        ->and($log)->toHaveCount(1)
        ->and($log[0])->toStartWith('PUT ')
        ->and(implode(' ', $log))->not->toContain('RevertToDraft')->not->toContain('Approve');
});

test('a local model is linked to the line on create', function () {
    $log = [];
    fakeLineXero($log);
    $model = lineSyncModel();

    lineSyncLine()->forLocalModel($model)->sendToXero(LINE_SYNC_TIMESHEET);

    $record = XeroRecord::where('xero_id', 'line-new')->first();

    expect($record)->not->toBeNull()
        ->and($record->recordable_id)->toBe('7')
        ->and($record->recordable_type)->toBe(LineSyncLocalModel::class);
});

test('a line synced earlier finds its local model again and keeps the link on update', function () {
    $log = [];
    fakeLineXero($log, ['timesheetLine' => ['timesheetLineID' => 'line-1']]);
    $tokenId = XeroToken::query()->value('id');
    XeroRecord::create(['recordable_type' => LineSyncLocalModel::class, 'recordable_id' => $tokenId, 'xero_id' => 'line-1']);

    $line = lineSyncLine('line-1');
    $line->sendToXero(LINE_SYNC_TIMESHEET);

    expect($line->getLocalModel())->toBeInstanceOf(LineSyncLocalModel::class)
        ->and($line->getLocalModel()->getKey())->toBe($tokenId)
        ->and(XeroRecord::where('xero_id', 'line-1')->count())->toBe(1);
});

test('a deleted line has its link removed', function () {
    $log = [];
    fakeLineXero($log);
    XeroRecord::factory()->create(['xero_id' => 'line-1']);
    XeroRecord::factory()->create(['xero_id' => 'line-other', 'recordable_id' => 2]);

    lineSyncLine('line-1')->deleteFromXero(LINE_SYNC_TIMESHEET);

    expect(XeroRecord::where('xero_id', 'line-1')->exists())->toBeFalse()
        ->and(XeroRecord::where('xero_id', 'line-other')->exists())->toBeTrue();
});

test('the line type declares timesheetLineID as its key', function () {
    expect((new \ReflectionProperty(XeroTimesheetLineData::class, 'key'))->getDefaultValue())->toBe('timesheetLineID');
});
