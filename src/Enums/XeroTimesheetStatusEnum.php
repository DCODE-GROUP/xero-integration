<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\PayrollAU\Timesheet as TimesheetAU;

enum XeroTimesheetStatusEnum: string
{
    case DRAFT = 'draft';
    case PROCESSED = 'processed';
    case APPROVED = 'approved';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PROCESSED => 'Processed',
            self::APPROVED => 'Approved',
        };
    }

    public function toXeroAUValue(): string
    {
        return match ($this) {
            self::DRAFT => TimesheetAU::STATUS_DRAFT,
            self::PROCESSED => TimesheetAU::STATUS_PROCESSED,
            self::APPROVED => TimesheetAU::STATUS_APPROVED,
        };
    }
}
