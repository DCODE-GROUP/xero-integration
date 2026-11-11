<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\PayrollAU\PayItem\EarningsRate;

enum XeroAllowanceCategoryEnum: string
{
    case NON_DEDUCTIBLE = 'non_deductible';
    case UNIFORM = 'uniform';
    case PRIVATE_VEHICLE = 'private_vehicle';
    case HOME_OFFICE = 'home_office';
    case TRANSPORT = 'transport';
    case GENERAL = 'general';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::NON_DEDUCTIBLE => 'Non-Deductible',
            self::UNIFORM => 'Uniform',
            self::PRIVATE_VEHICLE => 'Private Vehicle',
            self::HOME_OFFICE => 'Home Office',
            self::TRANSPORT => 'Transport',
            self::GENERAL => 'General',
            self::OTHER => 'Other',
        };
    }

    public function toXeroAUValue(): string
    {
        return match ($this) {
            self::NON_DEDUCTIBLE => EarningsRate::ALLOWANCECATEGORY_NONDEDUCTIBLE,
            self::UNIFORM => EarningsRate::ALLOWANCECATEGORY_UNIFORM,
            self::PRIVATE_VEHICLE => EarningsRate::ALLOWANCECATEGORY_PRIVATEVEHICLE,
            self::HOME_OFFICE => EarningsRate::ALLOWANCECATEGORY_HOMEOFFICE,
            self::TRANSPORT => EarningsRate::ALLOWANCECATEGORY_TRANSPORT,
            self::GENERAL => EarningsRate::ALLOWANCECATEGORY_GENERAL,
            self::OTHER => EarningsRate::ALLOWANCECATEGORY_OTHER,
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
