<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\XeroPaymentData;
use Dcodegroup\XeroIntegration\Enums\XeroPaymentStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroPaymentTypesEnum;

test('can instantiate XeroPaymentData with required fields', function () {
    $data = new XeroPaymentData(
        Invoice: null,
        Date: Carbon::parse('2024-01-15'),
        Amount: 115.00,
        Reference: null,
        PaymentType: XeroPaymentTypesEnum::ACCOUNTS_RECEIVABLE_PAYMENT,
    );

    expect($data->Amount)->toBe(115.00)
        ->and($data->PaymentType)->toBe(XeroPaymentTypesEnum::ACCOUNTS_RECEIVABLE_PAYMENT);
});

test('optional fields default to null', function () {
    $data = new XeroPaymentData(
        Invoice: null,
        Date: Carbon::parse('2024-01-15'),
        Amount: 100.00,
        Reference: null,
        PaymentType: XeroPaymentTypesEnum::ACCOUNTS_RECEIVABLE_PAYMENT,
    );

    expect($data->Invoice)->toBeNull()
        ->and($data->PaymentID)->toBeNull()
        ->and($data->Reference)->toBeNull()
        ->and($data->CreditNote)->toBeNull()
        ->and($data->Prepayment)->toBeNull()
        ->and($data->Overpayment)->toBeNull()
        ->and($data->Status)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroPaymentData(
        Invoice: null,
        Date: Carbon::parse('2024-01-15'),
        Amount: 115.00,
        Reference: null,
        PaymentType: XeroPaymentTypesEnum::ACCOUNTS_RECEIVABLE_PAYMENT,
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys([
        'PaymentID', 'Invoice', 'Date', 'Amount',
        'Reference', 'PaymentType', 'Status',
    ]);
});

test('toXeroArray returns correct values', function () {
    $data = new XeroPaymentData(
        Invoice: null,
        Date: Carbon::parse('2024-01-15'),
        Amount: 115.00,
        Reference: 'REF-001',
        PaymentType: XeroPaymentTypesEnum::ACCOUNTS_RECEIVABLE_PAYMENT,
        PaymentID: 'pay-uuid-123',
        Status: XeroPaymentStatusEnum::AUTHORISED,
    );

    $array = $data->toXeroArray();

    expect($array['PaymentID'])->toBe('pay-uuid-123')
        ->and($array['Amount'])->toBe(115.00)
        ->and($array['Reference'])->toBe('REF-001');
});
