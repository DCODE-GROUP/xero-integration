<?php

namespace DcodeGroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Payment as XeroPayment;

enum XeroPaymentStatusEnum: string
{
    case AUTHORISED = 'authorised';
    case DELETED = 'deleted';

    public function getLabel(): string
    {
        return match ($this) {
            self::AUTHORISED => 'Authorised',
            self::DELETED => 'Deleted',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::AUTHORISED => XeroPayment::PAYMENT_STATUS_AUTHORISED,
            self::DELETED => XeroPayment::PAYMENT_STATUS_DELETED,
        };
    }
}
