<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\PayrollAU\PayItem\EarningsRate;

enum XeroEarningsTypeEnum: string
{
    case ORDINARY_TIME_EARNINGS = 'ordinary_time_earnings';
    case OVERTIME_EARNINGS = 'overtime_earnings';
    case ALLOWANCE = 'allowance';
    case LUMP_SUM_A = 'lump_sum_a';
    case LUMP_SUM_B = 'lump_sum_b';
    case LUMP_SUM_D = 'lump_sum_d';
    case LUMP_SUM_E = 'lump_sum_e';
    case EMPLOYMENT_TERMINATION_PAYMENT = 'employment_termination_payment';
    case BONUSES_AND_COMMISSIONS = 'bonuses_and_commissions';
    case LUMP_SUM_W = 'lump_sum_w';
    case DIRECTORS_FEES = 'directors_fees';
    case PAID_PARENTAL_LEAVE = 'paid_parental_leave';
    case WORKERS_COMPENSATION = 'workers_compensation';

    public function getLabel(): string
    {
        return match ($this) {
            self::ORDINARY_TIME_EARNINGS => 'Ordinary Time Earnings',
            self::OVERTIME_EARNINGS => 'Overtime Earnings',
            self::ALLOWANCE => 'Allowance',
            self::LUMP_SUM_A => 'Lump Sum A',
            self::LUMP_SUM_B => 'Lump Sum B',
            self::LUMP_SUM_D => 'Lump Sum D',
            self::LUMP_SUM_E => 'Lump Sum E',
            self::EMPLOYMENT_TERMINATION_PAYMENT => 'Employment Termination Payment',
            self::BONUSES_AND_COMMISSIONS => 'Bonuses and Commissions',
            self::LUMP_SUM_W => 'Lump Sum W',
            self::DIRECTORS_FEES => 'Directors Fees',
            self::PAID_PARENTAL_LEAVE => 'Paid Parental Leave',
            self::WORKERS_COMPENSATION => 'Workers Compensation',
        };
    }

    public function toXeroAUValue(): string
    {
        return match ($this) {
            self::ORDINARY_TIME_EARNINGS => EarningsRate::EARNINGSTYPE_ORDINARYTIMEEARNINGS,
            self::OVERTIME_EARNINGS => EarningsRate::EARNINGSTYPE_OVERTIMEEARNINGS,
            self::ALLOWANCE => EarningsRate::EARNINGSTYPE_ALLOWANCE,
            self::LUMP_SUM_A => EarningsRate::EARNINGSTYPE_LUMPSUMA,
            self::LUMP_SUM_B => EarningsRate::EARNINGSTYPE_LUMPSUMB,
            self::LUMP_SUM_D => EarningsRate::EARNINGSTYPE_LUMPSUMD,
            self::LUMP_SUM_E => EarningsRate::EARNINGSTYPE_LUMPSUME,
            self::EMPLOYMENT_TERMINATION_PAYMENT => EarningsRate::EARNINGS_TYPE_EMPLOYMENTTERMINATIONPAYMENT,
            self::BONUSES_AND_COMMISSIONS => EarningsRate::EARNINGSTYPE_BONUSESANDCOMMISSIONS,
            self::LUMP_SUM_W => EarningsRate::EARNINGSTYPE_LUMPSUMW,
            self::DIRECTORS_FEES => EarningsRate::EARNINGSTYPE_DIRECTORSFEES,
            self::PAID_PARENTAL_LEAVE => EarningsRate::EARNINGSTYPE_PAIDPARENTALLEAVE,
            self::WORKERS_COMPENSATION => EarningsRate::EARNINGSTYPE_WORKERSCOMPENSATION,
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
