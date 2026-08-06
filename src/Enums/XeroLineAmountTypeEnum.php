<?php

namespace DcodeGroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\LineItem as XeroLineItem;

enum XeroLineAmountTypeEnum: string
{
    case EXCLUSIVE = 'exclusive';
    case INCLUSIVE = 'inclusive';
    case NO_TAX = 'no-tax';

    public function getLabel(): string
    {
        return match ($this) {
            self::EXCLUSIVE => 'Exclusive',
            self::INCLUSIVE => 'Inclusive',
            self::NO_TAX => 'No Tax',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::EXCLUSIVE => XeroLineItem::TYPE_EXCLUSIVE,
            self::INCLUSIVE => XeroLineItem::TYPE_INCLUSIVE,
            self::NO_TAX => XeroLineItem::TYPE_NOTAX,
        };
    }
}
