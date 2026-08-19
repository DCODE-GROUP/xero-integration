<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\XeroContactData;
use Dcodegroup\XeroIntegration\Data\XeroCreditNoteData;
use Dcodegroup\XeroIntegration\Enums\XeroContactStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroCreditNoteTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroInvoiceStatusEnum;

test('can instantiate XeroCreditNoteData with required fields', function () {
    $contact = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Acme Corp',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
    );

    $data = new XeroCreditNoteData(
        CreditNoteID: null,
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroInvoiceStatusEnum::AUTHORISED,
        SubTotal: 100.00,
        TotalTax: 15.00,
        Total: 115.00,
        Type: XeroCreditNoteTypeEnum::ACCRECCREDIT,
    );

    expect($data->SubTotal)->toBe(100.00)
        ->and($data->Total)->toBe(115.00)
        ->and($data->Status)->toBe(XeroInvoiceStatusEnum::AUTHORISED)
        ->and($data->Type)->toBe(XeroCreditNoteTypeEnum::ACCRECCREDIT);
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

    $data = new XeroCreditNoteData(
        CreditNoteID: null,
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroInvoiceStatusEnum::DRAFT,
        SubTotal: 0.00,
        TotalTax: 0.00,
        Total: 0.00,
        Type: XeroCreditNoteTypeEnum::ACCRECCREDIT,
    );

    expect($data->CreditNoteID)->toBeNull()
        ->and($data->CreditNoteNumber)->toBeNull()
        ->and($data->Reference)->toBeNull()
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

    $data = new XeroCreditNoteData(
        CreditNoteID: null,
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroInvoiceStatusEnum::AUTHORISED,
        SubTotal: 100.00,
        TotalTax: 15.00,
        Total: 115.00,
        Type: XeroCreditNoteTypeEnum::ACCRECCREDIT,
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys([
        'CreditNoteID', 'Contact', 'LineItems', 'Date',
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

    $data = new XeroCreditNoteData(
        CreditNoteID: 'cn-uuid-123',
        Contact: $contact,
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
        Status: XeroInvoiceStatusEnum::AUTHORISED,
        SubTotal: 100.00,
        TotalTax: 15.00,
        Total: 115.00,
        Type: XeroCreditNoteTypeEnum::ACCRECCREDIT,
    );

    $array = $data->toXeroArray();

    expect($array['CreditNoteID'])->toBe('cn-uuid-123')
        ->and($array['SubTotal'])->toBe(100.00)
        ->and($array['Total'])->toBe(115.00);
});
