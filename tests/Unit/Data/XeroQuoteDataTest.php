<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\XeroItemData;
use Dcodegroup\XeroIntegration\Data\XeroQuoteData;
use Dcodegroup\XeroIntegration\Enums\XeroQuoteStatusEnum;

test('can instantiate XeroQuoteData with required fields', function () {
    $data = new XeroQuoteData(
        Contact: null,
        Status: null,
        Date: Carbon::parse('2024-01-01'),
        ExpiryDate: Carbon::parse('2024-01-31'),
        LineItems: collect(),
        SubTotal: null,
        TotalTax: null,
        Total: null,
        TotalDiscount: null,
        UpdatedDateUTC: Carbon::now(),
        QuoteID: null,
        QuoteNumber: 'QU-001',
    );

    expect($data->QuoteNumber)->toBe('QU-001')
        ->and($data->Date->format('Y-m-d'))->toBe('2024-01-01')
        ->and($data->ExpiryDate->format('Y-m-d'))->toBe('2024-01-31');
});

test('optional fields default to null', function () {
    $data = new XeroQuoteData(
        Contact: null,
        Status: null,
        Date: Carbon::parse('2024-01-01'),
        ExpiryDate: Carbon::parse('2024-01-31'),
        LineItems: collect(),
        SubTotal: null,
        TotalTax: null,
        Total: null,
        TotalDiscount: null,
        UpdatedDateUTC: Carbon::now(),
        QuoteID: null,
        QuoteNumber: 'QU-001',
    );

    expect($data->QuoteID)->toBeNull()
        ->and($data->Contact)->toBeNull()
        ->and($data->Status)->toBeNull()
        ->and($data->SubTotal)->toBeNull()
        ->and($data->Total)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroQuoteData(
        Contact: null,
        Status: null,
        Date: Carbon::parse('2024-01-01'),
        ExpiryDate: Carbon::parse('2024-01-31'),
        LineItems: collect(),
        SubTotal: null,
        TotalTax: null,
        Total: null,
        TotalDiscount: null,
        UpdatedDateUTC: Carbon::now(),
        QuoteID: null,
        QuoteNumber: 'QU-001',
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys([
        'Contact', 'Status', 'Date', 'ExpiryDate', 'LineItems',
        'UpdatedDateUTC', 'QuoteID', 'QuoteNumber',
    ]);
});

test('toXeroArray returns correct values', function () {
    $data = new XeroQuoteData(
        Contact: null,
        Status: XeroQuoteStatusEnum::DRAFT,
        Date: Carbon::parse('2024-01-01'),
        ExpiryDate: Carbon::parse('2024-01-31'),
        LineItems: collect([
            new XeroItemData(LineItemID: null, Description: 'Service', Quantity: 1.0, UnitAmount: null, LineAmount: 500.00),
        ]),
        SubTotal: 500.00,
        TotalTax: 75.00,
        Total: 575.00,
        TotalDiscount: null,
        UpdatedDateUTC: Carbon::now(),
        QuoteID: 'quote-uuid-123',
        QuoteNumber: 'QU-001',
    );

    $array = $data->toXeroArray();

    expect($array['QuoteID'])->toBe('quote-uuid-123')
        ->and($array['QuoteNumber'])->toBe('QU-001')
        ->and($array['SubTotal'])->toBe(500.00)
        ->and($array['Total'])->toBe(575.00);
});

test('can instantiate XeroQuoteData with line items', function () {
    $lineItems = collect([
        new XeroItemData(LineItemID: null, Description: 'Consulting', Quantity: 2.0, UnitAmount: null, LineAmount: 400.00),
        new XeroItemData(LineItemID: null, Description: 'Materials', Quantity: 1.0, UnitAmount: null, LineAmount: 150.00),
    ]);

    $data = new XeroQuoteData(
        Contact: null,
        Status: null,
        Date: Carbon::parse('2024-01-01'),
        ExpiryDate: Carbon::parse('2024-01-31'),
        LineItems: $lineItems,
        SubTotal: null,
        TotalTax: null,
        Total: null,
        TotalDiscount: null,
        UpdatedDateUTC: Carbon::now(),
        QuoteID: null,
        QuoteNumber: 'QU-002',
    );

    expect($data->LineItems)->toHaveCount(2);
});
