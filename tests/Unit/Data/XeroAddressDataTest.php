<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Dcodegroup\XeroIntegration\Data\XeroAddressData;
use Mockery;
use XeroPHP\Models\Accounting\Address as XeroAddress;

test('can instantiate XeroAddressData with all fields', function () {
    $data = new XeroAddressData(
        AddressType: 'STREET',
        AddressLine1: '123 Main St',
        AddressLine2: 'Suite 4',
        AddressLine3: null,
        AddressLine4: null,
        City: 'Auckland',
        Region: 'Auckland',
        PostalCode: '1010',
        Country: 'New Zealand',
        AttentionTo: 'John Doe',
    );

    expect($data->AddressType)->toBe('STREET')
        ->and($data->AddressLine1)->toBe('123 Main St')
        ->and($data->City)->toBe('Auckland')
        ->and($data->PostalCode)->toBe('1010')
        ->and($data->Country)->toBe('New Zealand')
        ->and($data->AttentionTo)->toBe('John Doe');
});

test('can instantiate XeroAddressData with null fields', function () {
    $data = new XeroAddressData;

    expect($data->AddressType)->toBeNull()
        ->and($data->AddressLine1)->toBeNull()
        ->and($data->City)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroAddressData(
        AddressType: 'STREET',
        AddressLine1: '123 Main St',
        City: 'Auckland',
        PostalCode: '1010',
        Country: 'New Zealand',
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys([
        'AddressType', 'AddressLine1', 'AddressLine2', 'AddressLine3', 'AddressLine4',
        'City', 'Region', 'PostalCode', 'Country', 'AttentionTo',
    ]);
});

test('toXeroArray returns correct values', function () {
    $data = new XeroAddressData(
        AddressType: 'POBOX',
        AddressLine1: '456 Queen St',
        City: 'Wellington',
        PostalCode: '6011',
        Country: 'New Zealand',
    );

    $array = $data->toXeroArray();

    expect($array['AddressType'])->toBe('POBOX')
        ->and($array['AddressLine1'])->toBe('456 Queen St')
        ->and($array['City'])->toBe('Wellington')
        ->and($array['PostalCode'])->toBe('6011')
        ->and($array['Country'])->toBe('New Zealand');
});

test('fromXero maps xero model to XeroAddressData', function () {
    $xeroAddress = Mockery::mock(XeroAddress::class);
    $xeroAddress->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'AddressType' => 'STREET',
        'AddressLine1' => '789 King St',
        'AddressLine2' => null,
        'AddressLine3' => null,
        'AddressLine4' => null,
        'City' => 'Christchurch',
        'Region' => 'Canterbury',
        'PostalCode' => '8011',
        'Country' => 'New Zealand',
        'AttentionTo' => 'Jane Smith',
        default => null,
    });
    $xeroAddress->shouldReceive('offsetExists')->andReturn(true);

    $data = XeroAddressData::fromXero($xeroAddress);

    expect($data)->toBeInstanceOf(XeroAddressData::class)
        ->and($data->AddressType)->toBe('STREET')
        ->and($data->AddressLine1)->toBe('789 King St')
        ->and($data->City)->toBe('Christchurch')
        ->and($data->PostalCode)->toBe('8011');
});
