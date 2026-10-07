<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\Accounting\XeroContactData;
use Dcodegroup\XeroIntegration\Data\Accounting\XeroLineItemData;
use Dcodegroup\XeroIntegration\Data\Accounting\XeroPurchaseOrderData;
use Dcodegroup\XeroIntegration\Enums\XeroContactStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroLineAmountTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroPurchaseOrderStatusEnum;
use XeroPHP\Models\Accounting\Contact;
use XeroPHP\Models\Accounting\PurchaseOrder;

test('can instantiate XeroPurchaseOrderData with required fields', function () {
    $data = new XeroPurchaseOrderData(
        Contact: contactData(),
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
    );

    expect($data->Date->format('Y-m-d'))->toBe('2024-01-15')
        ->and($data->LineItems)->toBeEmpty()
        ->and($data->PurchaseOrderID)->toBeNull();
});

test('optional fields default to null', function () {
    $data = new XeroPurchaseOrderData(
        Contact: contactData(),
        LineItems: collect(),
        Date: Carbon::parse('2024-01-15'),
    );

    expect($data->DeliveryDate)->toBeNull()
        ->and($data->Status)->toBeNull()
        ->and($data->ExpectedArrivalDate)->toBeNull()
        ->and($data->UpdatedDateUTC)->toBeNull();
});

test('toXeroArray serializes purchase order fields and nested line items', function () {
    $data = new XeroPurchaseOrderData(
        Contact: contactData(),
        LineItems: collect([
            new XeroLineItemData(
                Description: 'Office chairs',
                Quantity: 2.0,
                LineAmount: 300.00,
                UnitAmount: 150.00,
            ),
        ]),
        Date: Carbon::parse('2024-01-15'),
        DeliveryDate: Carbon::parse('2024-01-22'),
        LineAmountTypes: XeroLineAmountTypeEnum::EXCLUSIVE,
        PurchaseOrderNumber: 'PO-001',
        Status: XeroPurchaseOrderStatusEnum::AUTHORISED,
        PurchaseOrderID: 'purchase-order-uuid',
        SubTotal: 300.00,
        TotalTax: 30.00,
        Total: 330.00,
    );

    $array = $data->toXeroArray();

    expect($array['PurchaseOrderID'])->toBe('purchase-order-uuid')
        ->and($array['PurchaseOrderNumber'])->toBe('PO-001')
        ->and($array['Status'])->toBe('AUTHORISED')
        ->and($array['LineAmountTypes'])->toBe('Exclusive')
        ->and($array['LineItems'])->toHaveCount(1)
        ->and($array['LineItems'][0]['Description'])->toBe('Office chairs');
});

test('fromXero maps a purchase order model and nested records', function () {
    $contact = mock(Contact::class);
    $contact->shouldReceive('offsetGet')->andReturnUsing(fn (string $key) => match ($key) {
        'ContactID' => 'contact-uuid',
        'ContactStatus' => 'ACTIVE',
        'Name' => 'Acme Corp',
        'UpdatedDateUTC' => Carbon::now(),
        default => null,
    });
    $contact->shouldReceive('offsetExists')->andReturn(true);

    $purchaseOrder = mock(PurchaseOrder::class);
    $purchaseOrder->shouldReceive('offsetGet')->andReturnUsing(fn (string $key) => match ($key) {
        'Contact' => $contact,
        'LineItems' => [],
        'Date' => Carbon::parse('2024-01-15'),
        'DeliveryDate' => Carbon::parse('2024-01-22'),
        'LineAmountTypes' => 'Exclusive',
        'PurchaseOrderNumber' => 'PO-001',
        'Status' => 'AUTHORISED',
        'PurchaseOrderID' => 'purchase-order-uuid',
        'Total' => 330.00,
        'UpdatedDateUTC' => Carbon::parse('2024-01-16 12:00:00'),
        default => null,
    });
    $purchaseOrder->shouldReceive('offsetExists')->andReturn(true);

    $data = XeroPurchaseOrderData::fromXero($purchaseOrder);

    expect($data)->toBeInstanceOf(XeroPurchaseOrderData::class)
        ->and($data->Contact)->toBeInstanceOf(XeroContactData::class)
        ->and($data->Contact->Name)->toBe('Acme Corp')
        ->and($data->PurchaseOrderID)->toBe('purchase-order-uuid')
        ->and($data->Status)->toBe(XeroPurchaseOrderStatusEnum::AUTHORISED)
        ->and($data->LineAmountTypes)->toBe(XeroLineAmountTypeEnum::EXCLUSIVE)
        ->and($data->Total)->toBe(330.00);
});

function contactData(): XeroContactData
{
    return new XeroContactData(
        ContactID: 'contact-uuid',
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Acme Corp',
        UpdatedDateUTC: Carbon::now(),
    );
}
