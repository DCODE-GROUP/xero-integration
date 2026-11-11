<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\PayrollAU\PayItem\EarningsRate;

enum XeroEmploymentTerminationPaymentTypeEnum: string
{
    case O = 'o';
    case R = 'r';

    public function getLabel(): string
    {
        return match ($this) {
            self::O => 'O',
            self::R => 'R',
        };
    }

    public function toXeroAUValue(): string
    {
        return match ($this) {
            self::O => EarningsRate::EMPLOYMENTTERMINATIONPAYMENTTYPE_O,
            self::R => EarningsRate::EMPLOYMENTTERMINATIONPAYMENTTYPE_R,
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
