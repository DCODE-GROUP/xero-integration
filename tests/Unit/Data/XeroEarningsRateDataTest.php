<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroEarningsRateData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Mockery;
use XeroPHP\Models\PayrollAU\PayItem\EarningsRate;

function makeEarningsRateData(array $overrides = []): XeroEarningsRateData
{
    return new XeroEarningsRateData(...array_merge([
        'Name' => 'Ordinary Hours',
        'AccountCode' => '477',
        'TypeOfUnits' => 'Hours',
        'IsExemptFromTax' => false,
        'IsExemptFromSuper' => false,
        'EarningsType' => 'ORDINARYTIMEEARNINGS',
    ], $overrides));
}

test('can instantiate XeroEarningsRateData with required fields', function () {
    $data = makeEarningsRateData();

    expect($data->Name)->toBe('Ordinary Hours')
        ->and($data->AccountCode)->toBe('477')
        ->and($data->TypeOfUnits)->toBe('Hours')
        ->and($data->IsExemptFromTax)->toBeFalse()
        ->and($data->IsExemptFromSuper)->toBeFalse()
        ->and($data->EarningsType)->toBe('ORDINARYTIMEEARNINGS');
});

test('optional fields default to null', function () {
    $data = makeEarningsRateData();

    expect($data->EarningsRateID)->toBeNull()
        ->and($data->RateType)->toBeNull()
        ->and($data->RatePerUnit)->toBeNull()
        ->and($data->Multiplier)->toBeNull()
        ->and($data->AccrueLeave)->toBeNull()
        ->and($data->AllowanceType)->toBeNull();
});

test('uses the earnings rate relationship', function () {
    expect(XeroEarningsRateData::getXeroRelationship())->toBe(XeroRelationshipsEnum::EARNINGS_RATE_AU)
        ->and(XeroRelationshipsEnum::EARNINGS_RATE_AU->getModelClass())->toBe(EarningsRate::class);
});

test('toXeroArray returns correct keys and values', function () {
    $data = makeEarningsRateData([
        'EarningsRateID' => 'rate-uuid',
        'RateType' => 'MULTIPLE',
        'Multiplier' => 1.5,
        'AccrueLeave' => true,
    ]);

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys([
        'Name', 'AccountCode', 'TypeOfUnits', 'IsExemptFromTax', 'IsExemptFromSuper',
        'EarningsType', 'EarningsRateID', 'RateType', 'IsReportableAsW1', 'RatePerUnit',
        'Multiplier', 'AccrueLeave', 'Amount', 'EmploymentTerminationPaymentType',
        'CurrentRecord', 'AllowanceType', 'AllowanceCategory',
    ])
        ->and($array['EarningsRateID'])->toBe('rate-uuid')
        ->and($array['RateType'])->toBe('MULTIPLE')
        ->and($array['Multiplier'])->toBe(1.5)
        ->and($array['AccrueLeave'])->toBeTrue();
});

test('fromXero maps xero model to XeroEarningsRateData', function () {
    $xeroRate = Mockery::mock(EarningsRate::class);
    $xeroRate->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'Name' => 'Overtime',
        'AccountCode' => '477',
        'TypeOfUnits' => 'Hours',
        'IsExemptFromTax' => false,
        'IsExemptFromSuper' => true,
        'EarningsType' => 'OVERTIMEEARNINGS',
        'EarningsRateID' => 'rate-uuid-123',
        'RateType' => 'MULTIPLE',
        'Multiplier' => '1.5',
        'AccrueLeave' => false,
        default => null,
    });
    $xeroRate->shouldReceive('offsetExists')->andReturn(true);

    $data = XeroEarningsRateData::fromXero($xeroRate);

    expect($data)->toBeInstanceOf(XeroEarningsRateData::class)
        ->and($data->Name)->toBe('Overtime')
        ->and($data->IsExemptFromSuper)->toBeTrue()
        ->and($data->EarningsType)->toBe('OVERTIMEEARNINGS')
        ->and($data->EarningsRateID)->toBe('rate-uuid-123')
        ->and($data->RateType)->toBe('MULTIPLE')
        ->and($data->Multiplier)->toBe(1.5)
        ->and($data->RatePerUnit)->toBeNull()
        ->and($data->AccrueLeave)->toBeFalse();
});
