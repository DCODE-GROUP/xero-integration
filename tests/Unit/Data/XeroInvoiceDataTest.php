<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\XeroContactData;
use Dcodegroup\XeroIntegration\Data\XeroInvoiceData;
use Dcodegroup\XeroIntegration\Data\XeroItemData;
use Dcodegroup\XeroIntegration\Enums\XeroContactStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroInvoiceStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroInvoiceTypeEnum;

test('can instantiate XeroInvoiceData with required fields', function () {
    $contact = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Acme Corp',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
    );

    $lineItems = collect([
        new XeroItemData(LineItemID: null, Description: 'Widget', Quantity: 1.0, UnitAmount: null, LineAmount: 100.00),
    ]);

    $data = new XeroInvoiceData(
        InvoiceID: null,
        Contact: $contact,
        LineItems: $lineItems,
        InvoiceDate: Carbon::parse('2024-01-01'),
        DueDate: Carbon::parse('2024-01-31'),
        InvoiceNumber: 'INV-001',
        Status: XeroInvoiceStatusEnum::AUTHORISED,
        Subtotal: 100.00,
        TaxAmount: 15.00,
        Total: 115.00,
        TotalDiscount: null,
        Payments: null,
        AmountDue: 115.00,
        AmountPaid: 0.00,
        UpdatedDateUTC: Carbon::now(),
        Type: XeroInvoiceTypeEnum::ACCREC,
    );

    expect($data->InvoiceNumber)->toBe('INV-001')
        ->and($data->Status)->toBe(XeroInvoiceStatusEnum::AUTHORISED)
        ->and($data->Total)->toBe(115.00)
        ->and($data->Type)->toBe(XeroInvoiceTypeEnum::ACCREC);
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

    $data = new XeroInvoiceData(
        InvoiceID: null,
        Contact: $contact,
        LineItems: collect(),
        InvoiceDate: Carbon::parse('2024-01-01'),
        DueDate: Carbon::parse('2024-01-31'),
        InvoiceNumber: 'INV-002',
        Status: XeroInvoiceStatusEnum::DRAFT,
        Subtotal: 0.00,
        TaxAmount: 0.00,
        Total: 0.00,
        TotalDiscount: null,
        Payments: null,
        AmountDue: 0.00,
        AmountPaid: 0.00,
        UpdatedDateUTC: null,
        Type: XeroInvoiceTypeEnum::ACCREC,
    );

    expect($data->InvoiceID)->toBeNull()
        ->and($data->TotalDiscount)->toBeNull()
        ->and($data->Payments)->toBeNull()
        ->and($data->Reference)->toBeNull();
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

    $data = new XeroInvoiceData(
        InvoiceID: null,
        Contact: $contact,
        LineItems: collect(),
        InvoiceDate: Carbon::parse('2024-01-01'),
        DueDate: Carbon::parse('2024-01-31'),
        InvoiceNumber: 'INV-001',
        Status: XeroInvoiceStatusEnum::AUTHORISED,
        Subtotal: 100.00,
        TaxAmount: 15.00,
        Total: 115.00,
        TotalDiscount: null,
        Payments: collect(),
        AmountDue: 115.00,
        AmountPaid: 0.00,
        UpdatedDateUTC: Carbon::now(),
        Type: XeroInvoiceTypeEnum::ACCREC,
        Prepayments: collect(),
        Overpayments: collect(),
        CreditNotes: collect(),
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys([
        'Type', 'Contact', 'LineItems', 'Date', 'DueDate',
        'InvoiceNumber', 'Status', 'Subtotal', 'TotalTax', 'Total',
        'AmountDue', 'AmountPaid', 'InvoiceID',
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

    $data = new XeroInvoiceData(
        InvoiceID: 'inv-uuid-123',
        Contact: $contact,
        LineItems: collect(),
        InvoiceDate: Carbon::parse('2024-01-01'),
        DueDate: Carbon::parse('2024-01-31'),
        InvoiceNumber: 'INV-001',
        Status: XeroInvoiceStatusEnum::AUTHORISED,
        Subtotal: 100.00,
        TaxAmount: 15.00,
        Total: 115.00,
        TotalDiscount: null,
        Payments: collect(),
        AmountDue: 115.00,
        AmountPaid: 0.00,
        UpdatedDateUTC: Carbon::now(),
        Type: XeroInvoiceTypeEnum::ACCREC,
        Prepayments: collect(),
        Overpayments: collect(),
        CreditNotes: collect(),
    );

    $array = $data->toXeroArray();

    expect($array['InvoiceNumber'])->toBe('INV-001')
        ->and($array['InvoiceID'])->toBe('inv-uuid-123')
        ->and($array['Subtotal'])->toBe(100.00)
        ->and($array['Total'])->toBe(115.00);
});
