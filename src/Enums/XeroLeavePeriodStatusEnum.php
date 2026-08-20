<?php

namespace Dcodegroup\XeroIntegration\Enums;

enum XeroLeavePeriodStatusEnum: string
{
    case REQUESTED = 'requested';
    case SCHEDULED = 'scheduled';
    case PROCESSED = 'processed';
    case REJECTED = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::REQUESTED => 'Requested',
            self::SCHEDULED => 'Scheduled',
            self::PROCESSED => 'Processed',
            self::REJECTED => 'Rejected',
        };
    }

    public function toXeroAUValue(): string
    {
        return match ($this) {
            self::REQUESTED => 'REQUESTED',
            self::SCHEDULED => 'SCHEDULED',
            self::PROCESSED => 'PROCESSED',
            self::REJECTED => 'REJECTED',
        };
    }
}
