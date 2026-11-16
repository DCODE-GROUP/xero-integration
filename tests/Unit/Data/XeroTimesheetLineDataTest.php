<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroTimesheetLineData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use LogicException;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\PayrollAU\Timesheet;

test('it uses the Payroll AU 2.0 attributes', function () {
    $line = new XeroTimesheetLineData(
        date: Carbon::parse('2026-11-03'),
        earningsRateID: 'rate-uuid',
        numberOfUnits: 7.5,
    );

    expect(array_keys(get_object_vars($line)))->toContain('date', 'earningsRateID', 'numberOfUnits', 'timesheetLineID', 'trackingItemID')
        ->and(XeroTimesheetLineData::getXeroRelationship())->toBe(XeroRelationshipsEnum::TIMESHEET_LINE_AU);
});

test('optional attributes default to null', function () {
    $line = new XeroTimesheetLineData(Carbon::parse('2026-11-03'), 'rate-uuid', 8.0);

    expect($line->timesheetLineID)->toBeNull()
        ->and($line->trackingItemID)->toBeNull();
});

test('fromXeroArray hydrates a line', function () {
    $line = XeroTimesheetLineData::fromXeroArray([
        'timesheetLineID' => 'line-1',
        'date' => '2026-11-03T00:00:00',
        'earningsRateID' => 'rate-uuid',
        'trackingItemID' => null,
        'numberOfUnits' => 6,
    ]);

    expect($line->date->toDateString())->toBe('2026-11-03')
        ->and($line->earningsRateID)->toBe('rate-uuid')
        ->and($line->numberOfUnits)->toBe(6.0)
        ->and($line->timesheetLineID)->toBe('line-1')
        ->and($line->trackingItemID)->toBeNull();
});

test('toXeroPayload sends only writable attributes, with a plain date', function () {
    $line = new XeroTimesheetLineData(Carbon::parse('2026-11-03'), 'rate-uuid', 7.5, timesheetLineID: 'line-1');

    expect($line->toXeroPayload())->toBe([
        'date' => '2026-11-03',
        'earningsRateID' => 'rate-uuid',
        'numberOfUnits' => 7.5,
    ]);
});

test('toXeroPayload includes a tracking item when set', function () {
    $line = new XeroTimesheetLineData(Carbon::parse('2026-11-03'), 'rate-uuid', 2.0, trackingItemID: 'track-1');

    expect($line->toXeroPayload()['trackingItemID'])->toBe('track-1');
});

test('toXeroPayload omits unset optionals', function () {
    $line = new XeroTimesheetLineData(Carbon::parse('2026-11-03'), 'rate-uuid', 2.0, timesheetLineID: Optional::create(), trackingItemID: Optional::create());

    expect($line->toXeroPayload())->not->toHaveKeys(['timesheetLineID', 'trackingItemID']);
});

test('a line is identified by date, earnings rate and tracking item', function () {
    $a = new XeroTimesheetLineData(Carbon::parse('2026-11-03'), 'rate-1', 1.0);
    $b = new XeroTimesheetLineData(Carbon::parse('2026-11-03'), 'rate-1', 9.0);
    $c = new XeroTimesheetLineData(Carbon::parse('2026-11-03'), 'rate-2', 1.0);
    $d = new XeroTimesheetLineData(Carbon::parse('2026-11-03'), 'rate-1', 1.0, trackingItemID: 'track-1');

    expect($a->lineKey())->toBe($b->lineKey())
        ->and($a->lineKey())->not->toBe($c->lineKey())
        ->and($a->lineKey())->not->toBe($d->lineKey());
});

test('fromXero is not supported because xero-php has no 2.0 model', function () {
    XeroTimesheetLineData::fromXero(new Timesheet);
})->throws(LogicException::class, '2.0');
