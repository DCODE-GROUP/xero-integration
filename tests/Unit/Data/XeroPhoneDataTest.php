<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Dcodegroup\XeroIntegration\Data\Accounting\XeroPhoneData;
use Dcodegroup\XeroIntegration\Enums\XeroPhoneTypeEnum;
use XeroPHP\Models\Accounting\Phone;

use function Pest\Laravel\mock;

test('can instantiate XeroPhoneData with all fields', function () {
    $data = new XeroPhoneData(
        PhoneType: XeroPhoneTypeEnum::DEFAULT,
        PhoneNumber: '1234567',
        PhoneAreaCode: '09',
        PhoneCountryCode: '64',
    );

    expect($data->PhoneType)->toBe(XeroPhoneTypeEnum::DEFAULT)
        ->and($data->PhoneNumber)->toBe('1234567')
        ->and($data->PhoneAreaCode)->toBe('09')
        ->and($data->PhoneCountryCode)->toBe('64');
});

test('can instantiate XeroPhoneData with null fields', function () {
    $data = new XeroPhoneData;

    expect($data->PhoneType)->toBeNull()
        ->and($data->PhoneNumber)->toBeNull()
        ->and($data->PhoneAreaCode)->toBeNull()
        ->and($data->PhoneCountryCode)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroPhoneData(
        PhoneType: XeroPhoneTypeEnum::MOBILE,
        PhoneNumber: '9876543',
        PhoneAreaCode: '021',
        PhoneCountryCode: '64',
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys(['PhoneType', 'PhoneNumber', 'PhoneAreaCode', 'PhoneCountryCode']);
});

test('toXeroArray returns correct values', function () {
    $data = new XeroPhoneData(
        PhoneType: XeroPhoneTypeEnum::MOBILE,
        PhoneNumber: '5551234',
        PhoneAreaCode: '021',
        PhoneCountryCode: '64',
    );

    $array = $data->toXeroArray();

    expect($array['PhoneNumber'])->toBe('5551234')
        ->and($array['PhoneAreaCode'])->toBe('021')
        ->and($array['PhoneCountryCode'])->toBe('64');
});

test('fromXero maps xero model to XeroPhoneData', function () {
    $xeroPhone = mock(Phone::class);
    $xeroPhone->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'PhoneType' => 'DEFAULT',
        'PhoneNumber' => '1234567',
        'PhoneAreaCode' => '09',
        'PhoneCountryCode' => '64',
        default => null,
    });

    $xeroPhone->shouldReceive('offsetExists')->andReturn(true);

    $data = XeroPhoneData::fromXero($xeroPhone);

    expect($data)->toBeInstanceOf(XeroPhoneData::class)
        ->and($data->PhoneNumber)->toBe('1234567')
        ->and($data->PhoneAreaCode)->toBe('09')
        ->and($data->PhoneCountryCode)->toBe('64');
});
