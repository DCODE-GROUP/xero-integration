<?php

namespace DcodeGroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Overpayment as XeroOverPayment;

enum XeroOverpaymentTypeEnum: string
{
    case RECEIVE_OVERPAYMENT = 'receive-overpayment';
    case SPEND_OVERPAYMENT = 'spend-overpayment';

    public function getLabel(): string
    {
        return match ($this) {
            self::RECEIVE_OVERPAYMENT => 'Receive Overpayment',
            self::SPEND_OVERPAYMENT => 'Spend Overpayment',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::RECEIVE_OVERPAYMENT => XeroOverPayment::TYPE_RECEIVE_OVERPAYMENT,
            self::SPEND_OVERPAYMENT => XeroOverPayment::TYPE_SPEND_OVERPAYMENT,
        };
    }
}
