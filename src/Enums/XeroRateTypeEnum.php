<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\PayrollAU\PayItem\EarningsRate;

enum XeroRateTypeEnum: string
{
    case FIXED_AMOUNT = 'fixed_amount';
    case MULTIPLE = 'multiple';
    case RATE_PER_UNIT = 'rate_per_unit';

    public function getLabel(): string
    {
        return match ($this) {
            self::FIXED_AMOUNT => 'Fixed Amount',
            self::MULTIPLE => 'Multiple',
            self::RATE_PER_UNIT => 'Rate Per Unit',
        };
    }

    public function toXeroAUValue(): string
    {
        return match ($this) {
            self::FIXED_AMOUNT => EarningsRate::RATETYPE_FIXEDAMOUNT,
            self::MULTIPLE => EarningsRate::RATETYPE_MULTIPLE,
            self::RATE_PER_UNIT => EarningsRate::RATETYPE_RATEPERUNIT,
        };
    }

    public static function fromXeroAUValue(?string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->toXeroAUValue() === $value) {
                return $case;
            }
        }

        return null;
    }
}
