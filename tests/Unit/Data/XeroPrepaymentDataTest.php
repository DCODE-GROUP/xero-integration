<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\XeroContactData;
use Dcodegroup\XeroIntegration\Data\XeroPrepaymentData;
use Dcodegroup\XeroIntegration\Enums\XeroContactStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroPrepaymentStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroPrepaymentTypeEnum;

test('can instantiate XeroPrepaymentData with required fields', function () {
    $contact = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Acme Corp',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
    );

    $data = new XeroPrepaymentData(
        PrepaymentID: null,
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroPrepaymentStatusEnum::AUTHORISED,
        SubTotal: 200.00,
        TotalTax: 30.00,
        Total: 230.00,
        Type: XeroPrepaymentTypeEnum::RECEIVE_PREPAYMENT,
    );

    expect($data->SubTotal)->toBe(200.00)
        ->and($data->Total)->toBe(230.00)
        ->and($data->Status)->toBe(XeroPrepaymentStatusEnum::AUTHORISED)
        ->and($data->Type)->toBe(XeroPrepaymentTypeEnum::RECEIVE_PREPAYMENT);
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

    $data = new XeroPrepaymentData(
        PrepaymentID: null,
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroPrepaymentStatusEnum::AUTHORISED,
        SubTotal: 0.00,
        TotalTax: 0.00,
        Total: 0.00,
        Type: XeroPrepaymentTypeEnum::SPEND_PREPAYMENT,
    );

    expect($data->PrepaymentID)->toBeNull()
        ->and($data->CurrencyCode)->toBeNull()
        ->and($data->UpdatedDateUTC)->toBeNull();
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

    $data = new XeroPrepaymentData(
        PrepaymentID: null,
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroPrepaymentStatusEnum::AUTHORISED,
        SubTotal: 200.00,
        TotalTax: 30.00,
        Total: 230.00,
        Type: XeroPrepaymentTypeEnum::RECEIVE_PREPAYMENT,
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys([
        'PrepaymentID', 'Contact', 'LineItems', 'Date',
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

    $data = new XeroPrepaymentData(
        PrepaymentID: 'pp-uuid-123',
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroPrepaymentStatusEnum::AUTHORISED,
        SubTotal: 200.00,
        TotalTax: 30.00,
        Total: 230.00,
        Type: XeroPrepaymentTypeEnum::RECEIVE_PREPAYMENT,
    );

    $array = $data->toXeroArray();

    expect($array['PrepaymentID'])->toBe('pp-uuid-123')
        ->and($array['SubTotal'])->toBe(200.00)
        ->and($array['Total'])->toBe(230.00);
});
