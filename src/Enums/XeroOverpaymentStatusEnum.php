<?php

namespace DcodeGroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Overpayment as XeroOverpayment;

enum XeroOverpaymentStatusEnum: string
{
    case AUTHORISED = 'authorised';
    case PAID = 'paid';
    case VOIDED = 'voided';

    public function getLabel(): string
    {
        return match ($this) {
            self::AUTHORISED => 'Authorised',
            self::PAID => 'Paid',
            self::VOIDED => 'Voided',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::AUTHORISED => XeroOverpayment::OVERPAYMENT_STATUS_AUTHORISED,
            self::PAID => XeroOverpayment::OVERPAYMENT_STATUS_PAID,
            self::VOIDED => XeroOverpayment::OVERPAYMENT_STATUS_VOIDED,
        };
    }
}
