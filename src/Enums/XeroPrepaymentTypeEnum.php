<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Prepayment as XeroPrepayment;

enum XeroPrepaymentTypeEnum: string
{
    case RECEIVE_PREPAYMENT = 'receive-prepayment';
    case SPEND_PREPAYMENT = 'spend-prepayment';

    public function getLabel(): string
    {
        return match ($this) {
            self::RECEIVE_PREPAYMENT => 'Receive Prepayment',
            self::SPEND_PREPAYMENT => 'Spend Prepayment',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::RECEIVE_PREPAYMENT => XeroPrepayment::TYPE_RECEIVE_PREPAYMENT,
            self::SPEND_PREPAYMENT => XeroPrepayment::TYPE_SPEND_PREPAYMENT,
        };
    }
}
