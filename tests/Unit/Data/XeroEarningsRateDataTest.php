<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroEarningsRateData;
use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\ListXeroRecordsRequest;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Saloon\Http\Faking\MockResponse;
use XeroPHP\Models\PayrollAU\PayItem;
use XeroPHP\Models\PayrollAU\PayItem\EarningsRate;

/**
 * @param  array<int, array<string, mixed>>  $rates
 */
function fakePayItems(array $rates): void
{
    XeroToken::factory()->create(['current_tenant_id' => 'tenant-1']);

    fakeSaloon([
        ListXeroRecordsRequest::class => MockResponse::make(['PayItems' => ['EarningsRates' => $rates]]),
    ]);
}

test('maps to the PayItems resource', function () {
    expect(XeroRelationshipsEnum::EARNINGS_RATE_AU->getModelClass())->toBe(PayItem::class)
        ->and(XeroEarningsRateData::getXeroRelationship())->toBe(XeroRelationshipsEnum::EARNINGS_RATE_AU);
});

test('fromXero is not supported because earnings rates are read through the direct API', function () {
    XeroEarningsRateData::fromXero(new EarningsRate);
})->throws(\LogicException::class, 'fromXeroArray');

test('it is served by the Payroll AU 1.0 PayItems endpoint', function () {
    $endpoint = XeroEarningsRateData::getXeroEndpoint();

    expect($endpoint->api)->toBe(XeroApiEnum::PAYROLL_AU)
        ->and($endpoint->resource)->toBe('PayItems')
        ->and($endpoint->resolvedListKey())->toBe('PayItems.EarningsRates');
});

test('fromXeroArray maps an earnings rate', function () {
    $data = XeroEarningsRateData::fromXeroArray([
        'EarningsRateID' => 'rate-uuid',
        'Name' => 'Ordinary Hours',
        'EarningsType' => 'ORDINARYTIMEEARNINGS',
        'RatePerUnit' => '42.5',
        'CurrentRecord' => true,
    ]);

    expect($data->EarningsRateID)->toBe('rate-uuid')
        ->and($data->EarningsType)->toBe('ORDINARYTIMEEARNINGS')
        ->and($data->RatePerUnit)->toBe(42.5)
        ->and($data->Multiplier)->toBeNull()
        ->and($data->CurrentRecord)->toBeTrue();
});

test('list flattens earnings rates out of the pay items', function () {
    fakePayItems([
        ['EarningsRateID' => 'rate-1', 'Name' => 'Ordinary Hours'],
        ['EarningsRateID' => 'rate-2', 'Name' => 'Overtime'],
    ]);

    $rates = XeroEarningsRateData::list();

    expect($rates)->toHaveCount(2)
        ->and($rates->pluck('EarningsRateID')->all())->toBe(['rate-1', 'rate-2'])
        ->and($rates->first())->toBeInstanceOf(XeroEarningsRateData::class);
});

test('list excludes rates that are not current', function () {
    fakePayItems([
        ['EarningsRateID' => 'current', 'Name' => 'Current', 'CurrentRecord' => true],
        ['EarningsRateID' => 'unflagged', 'Name' => 'Unflagged'],
        ['EarningsRateID' => 'old', 'Name' => 'Old', 'CurrentRecord' => false],
    ]);

    expect(XeroEarningsRateData::list()->pluck('EarningsRateID')->all())->toBe(['current', 'unflagged']);
});

test('list is empty when Xero returns no earnings rates', function () {
    fakePayItems([]);

    expect(XeroEarningsRateData::list())->toBeEmpty();
});

test('is read-only', function () {
    XeroEarningsRateData::fromXeroArray(['EarningsRateID' => 'rate-uuid', 'Name' => 'Ordinary Hours'])->toXeroArray();
})->throws(\LogicException::class);
