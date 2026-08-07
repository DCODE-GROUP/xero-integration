<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Prepayment as XeroPrepayment;

enum XeroPrepaymentStatusEnum: string
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
            self::AUTHORISED => XeroPrepayment::PREPAYMENT_STATUS_AUTHORISED,
            self::PAID => XeroPrepayment::PREPAYMENT_STATUS_PAID,
            self::VOIDED => XeroPrepayment::PREPAYMENT_STATUS_VOIDED,
        };
    }
}
