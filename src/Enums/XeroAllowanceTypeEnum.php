<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\PayrollAU\PayItem\EarningsRate;

enum XeroAllowanceTypeEnum: string
{
    case CAR = 'car';
    case TRANSPORT = 'transport';
    case TRAVEL = 'travel';
    case LAUNDRY = 'laundry';
    case MEALS = 'meals';
    case JOBKEEPER = 'jobkeeper';
    case TOOLS = 'tools';
    case TASKS = 'tasks';
    case QUALIFICATIONS = 'qualifications';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::CAR => 'Car',
            self::TRANSPORT => 'Transport',
            self::TRAVEL => 'Travel',
            self::LAUNDRY => 'Laundry',
            self::MEALS => 'Meals',
            self::JOBKEEPER => 'JobKeeper',
            self::TOOLS => 'Tools',
            self::TASKS => 'Tasks',
            self::QUALIFICATIONS => 'Qualifications',
            self::OTHER => 'Other',
        };
    }

    public function toXeroAUValue(): string
    {
        return match ($this) {
            self::CAR => EarningsRate::ALLOWANCETYPE_CAR,
            self::TRANSPORT => EarningsRate::ALLOWANCETYPE_TRANSPORT,
            self::TRAVEL => EarningsRate::ALLOWANCETYPE_TRAVEL,
            self::LAUNDRY => EarningsRate::ALLOWANCETYPE_LAUNDRY,
            self::MEALS => EarningsRate::ALLOWANCETYPE_MEALS,
            self::JOBKEEPER => EarningsRate::ALLOWANCETYPE_JOBKEEPER,
            self::TOOLS => EarningsRate::ALLOWANCETYPE_TOOLS,
            self::TASKS => EarningsRate::ALLOWANCETYPE_TASKS,
            self::QUALIFICATIONS => EarningsRate::ALLOWANCETYPE_QUALIFICATIONS,
            self::OTHER => EarningsRate::ALLOWANCETYPE_OTHER,
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
