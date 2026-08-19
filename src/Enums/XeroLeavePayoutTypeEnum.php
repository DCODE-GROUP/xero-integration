<?php

namespace Dcodegroup\XeroIntegration\Enums;

enum XeroLeavePayoutTypeEnum: string
{
    case DEFAULT = 'default';
    case CASHED_OUT = 'cashed_out';

    public function getLabel(): string
    {
        return match ($this) {
            self::DEFAULT => 'Default',
            self::CASHED_OUT => 'Cashed Out',
        };
    }

    public function toXeroAUValue(): string
    {
        return match ($this) {
            self::DEFAULT => 'DEFAULT',
            self::CASHED_OUT => 'CASHED_OUT',
        };
    }
}
