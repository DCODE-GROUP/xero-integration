<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\XeroContactData;
use Dcodegroup\XeroIntegration\Data\XeroOverpaymentData;
use Dcodegroup\XeroIntegration\Enums\XeroContactStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroOverpaymentStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroOverpaymentTypeEnum;

test('can instantiate XeroOverpaymentData with required fields', function () {
    $contact = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Acme Corp',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
    );

    $data = new XeroOverpaymentData(
        OverpaymentID: null,
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroOverpaymentStatusEnum::AUTHORISED,
        SubTotal: 300.00,
        TotalTax: 45.00,
        Total: 345.00,
        Type: XeroOverpaymentTypeEnum::RECEIVE_OVERPAYMENT,
    );

    expect($data->SubTotal)->toBe(300.00)
        ->and($data->Total)->toBe(345.00)
        ->and($data->Status)->toBe(XeroOverpaymentStatusEnum::AUTHORISED)
        ->and($data->Type)->toBe(XeroOverpaymentTypeEnum::RECEIVE_OVERPAYMENT);
});

test('optional fields default to null', function () {
    $contact = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Test Co',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
    );

    $data = new XeroOverpaymentData(
        OverpaymentID: null,
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroOverpaymentStatusEnum::AUTHORISED,
        SubTotal: 0.00,
        TotalTax: 0.00,
        Total: 0.00,
        Type: XeroOverpaymentTypeEnum::SPEND_OVERPAYMENT,
    );

    expect($data->OverpaymentID)->toBeNull()
        ->and($data->CurrencyCode)->toBeNull()
        ->and($data->UpdatedDateUTC)->toBeNull()
        ->and($data->Payments)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $contact = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Acme Corp',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
        ContactPersons: collect(),
        Addresses: collect(),
        Phones: collect(),
    );

    $data = new XeroOverpaymentData(
        OverpaymentID: null,
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroOverpaymentStatusEnum::AUTHORISED,
        SubTotal: 300.00,
        TotalTax: 45.00,
        Total: 345.00,
        Type: XeroOverpaymentTypeEnum::RECEIVE_OVERPAYMENT,
        Payments: collect(),
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys([
        'OverpaymentID', 'Contact', 'LineItems', 'Date',
        'Status', 'SubTotal', 'TotalTax', 'Total', 'Type',
    ]);
});

test('toXeroArray returns correct values', function () {
    $contact = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Acme Corp',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
        ContactPersons: collect(),
        Addresses: collect(),
        Phones: collect(),
    );

    $data = new XeroOverpaymentData(
        OverpaymentID: 'op-uuid-123',
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroOverpaymentStatusEnum::AUTHORISED,
        SubTotal: 300.00,
        TotalTax: 45.00,
        Total: 345.00,
        Type: XeroOverpaymentTypeEnum::RECEIVE_OVERPAYMENT,
        Payments: collect(),
    );

    $array = $data->toXeroArray();

    expect($array['OverpaymentID'])->toBe('op-uuid-123')
        ->and($array['SubTotal'])->toBe(300.00)
        ->and($array['Total'])->toBe(345.00);
});
