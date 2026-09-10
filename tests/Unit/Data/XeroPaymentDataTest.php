<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Contracts\XeroDataFinder;
use Dcodegroup\XeroIntegration\Data\Accounting\XeroPaymentData;
use Dcodegroup\XeroIntegration\Enums\XeroPaymentStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroPaymentTypesEnum;
use Mockery;
use XeroPHP\Remote\Model;

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

test('find delegates to the shared data finder', function () {
    $xeroPayment = Mockery::mock(Model::class);
    $xeroPayment->shouldReceive('offsetExists')->andReturnUsing(
        fn (string $key): bool => in_array($key, ['Date', 'Amount'], true)
    );
    $xeroPayment->shouldReceive('offsetGet')->andReturnUsing(
        fn (string $key): string|float => match ($key) {
            'Date' => '2024-01-15',
            'Amount' => 115.00,
        }
    );

    $this->mock(XeroDataFinder::class)
        ->shouldReceive('find')
        ->once()
        ->with(XeroPaymentData::class, 'pay-uuid-123')
        ->andReturn($xeroPayment);

    expect(XeroPaymentData::find('pay-uuid-123'))
        ->toBeInstanceOf(XeroPaymentData::class);
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
