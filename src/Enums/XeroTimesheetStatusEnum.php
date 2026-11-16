<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\PayrollAU\Timesheet as TimesheetAU;

/**
 * Payroll AU 2.0 timesheets are Draft, Approved or Completed. Processed is the 1.0 equivalent of Completed.
 */
enum XeroTimesheetStatusEnum: string
{
    case DRAFT = 'draft';
    case PROCESSED = 'processed';
    case APPROVED = 'approved';
    case COMPLETED = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PROCESSED => 'Processed',
            self::APPROVED => 'Approved',
            self::COMPLETED => 'Completed',
        };
    }

    public function toXeroAUValue(): string
    {
        return match ($this) {
            self::DRAFT => TimesheetAU::STATUS_DRAFT,
            self::PROCESSED => TimesheetAU::STATUS_PROCESSED,
            self::APPROVED => TimesheetAU::STATUS_APPROVED,
            self::COMPLETED => 'Completed',
        };
    }

    /**
     * Whether Xero has locked the timesheet against changes.
     */
    public function isLocked(): bool
    {
        return $this === self::COMPLETED || $this === self::PROCESSED;
    }
}
