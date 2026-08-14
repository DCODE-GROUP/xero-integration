<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Dcodegroup\XeroIntegration\Data\XeroItemData;
use Mockery;
use XeroPHP\Models\Accounting\LineItem;

test('can instantiate XeroItemData with required fields', function () {
    $data = new XeroItemData(
        LineItemID: null,
        Description: 'Widget A',
        Quantity: 2.0,
        UnitAmount: null,
        LineAmount: 100.00,
    );

    expect($data->Description)->toBe('Widget A')
        ->and($data->Quantity)->toBe(2.0)
        ->and($data->LineAmount)->toBe(100.00);
});

test('can instantiate XeroItemData with all fields', function () {
    $data = new XeroItemData(
        LineItemID: null,
        Description: 'Widget B',
        Quantity: 3.0,
        UnitAmount: 50.00,
        LineAmount: 150.00,
        TaxAmount: 15.00,
        ItemCode: 'WDG-B',
        AccountCode: '200',
        TaxType: 'OUTPUT',
    );

    expect($data->Description)->toBe('Widget B')
        ->and($data->Quantity)->toBe(3.0)
        ->and($data->LineAmount)->toBe(150.00)
        ->and($data->UnitAmount)->toBe(50.00)
        ->and($data->TaxAmount)->toBe(15.00)
        ->and($data->ItemCode)->toBe('WDG-B')
        ->and($data->AccountCode)->toBe('200')
        ->and($data->TaxType)->toBe('OUTPUT');
});

test('optional fields default to null', function () {
    $data = new XeroItemData(
        LineItemID: null,
        Description: 'Widget',
        Quantity: 1.0,
        UnitAmount: null,
        LineAmount: 50.00,
    );

    expect($data->LineItemID)->toBeNull()
        ->and($data->UnitAmount)->toBeNull()
        ->and($data->TaxAmount)->toBeNull()
        ->and($data->ItemCode)->toBeNull()
        ->and($data->AccountCode)->toBeNull()
        ->and($data->TaxType)->toBeNull()
        ->and($data->Tracking)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroItemData(
        LineItemID: null,
        Description: 'Widget',
        Quantity: 1.0,
        UnitAmount: null,
        LineAmount: 50.00,
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys([
        'LineItemID', 'Description', 'Quantity', 'UnitAmount', 'LineAmount',
        'TaxAmount', 'ItemCode', 'AccountCode', 'TaxType',
    ]);
});

test('toXeroArray returns correct values', function () {
    $data = new XeroItemData(
        LineItemID: null,
        Description: 'Service Fee',
        Quantity: 1.0,
        UnitAmount: 200.00,
        LineAmount: 200.00,
        ItemCode: 'SVC',
    );

    $array = $data->toXeroArray();

    expect($array['Description'])->toBe('Service Fee')
        ->and($array['Quantity'])->toBe(1.0)
        ->and($array['LineAmount'])->toBe(200.00)
        ->and($array['UnitAmount'])->toBe(200.00)
        ->and($array['ItemCode'])->toBe('SVC');
});

test('fromXero maps xero model to XeroItemData', function () {
    $lineItem = Mockery::mock(LineItem::class);
    $lineItem->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'LineItemID' => 'line-uuid-123',
        'Description' => 'Consulting',
        'Quantity' => 2.0,
        'UnitAmount' => 100.00,
        'LineAmount' => 200.00,
        'TaxAmount' => 20.00,
        'ItemCode' => 'CONS',
        'AccountCode' => '400',
        'AccountId' => null,
        'TaxType' => 'OUTPUT',
        'DiscountAmount' => null,
        'DiscountRate' => null,
        'Tracking' => null,
        'RepeatingInvoiceID' => null,
        default => null,
    });
    $lineItem->shouldReceive('offsetExists')->andReturn(true);

    $data = XeroItemData::fromXero($lineItem);

    expect($data)->toBeInstanceOf(XeroItemData::class)
        ->and($data->Description)->toBe('Consulting')
        ->and($data->Quantity)->toBe(2.0)
        ->and($data->LineAmount)->toBe(200.00)
        ->and($data->TaxAmount)->toBe(20.00)
        ->and($data->ItemCode)->toBe('CONS');
});
