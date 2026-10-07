<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Enums;

use Dcodegroup\XeroIntegration\Enums\XeroPurchaseOrderStatusEnum;

test('maps purchase order statuses to Xero values', function () {
    expect(XeroPurchaseOrderStatusEnum::DRAFT->getXeroValue())->toBe('DRAFT')
        ->and(XeroPurchaseOrderStatusEnum::SUBMITTED->getXeroValue())->toBe('SUBMITTED')
        ->and(XeroPurchaseOrderStatusEnum::AUTHORISED->getXeroValue())->toBe('AUTHORISED')
        ->and(XeroPurchaseOrderStatusEnum::BILLED->getXeroValue())->toBe('BILLED')
        ->and(XeroPurchaseOrderStatusEnum::DELETED->getXeroValue())->toBe('DELETED');
});

test('creates a status enum from Xero values case insensitively', function () {
    expect(XeroPurchaseOrderStatusEnum::fromXero('AUTHORISED'))
        ->toBe(XeroPurchaseOrderStatusEnum::AUTHORISED)
        ->and(XeroPurchaseOrderStatusEnum::fromXero('billed'))
        ->toBe(XeroPurchaseOrderStatusEnum::BILLED)
        ->and(XeroPurchaseOrderStatusEnum::fromXero(null))->toBeNull();
});
