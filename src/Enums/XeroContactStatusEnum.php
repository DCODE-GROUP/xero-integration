<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Contact as XeroContact;

enum XeroContactStatusEnum: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::ARCHIVED => 'Archived',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::ACTIVE => XeroContact::CONTACT_STATUS_ACTIVE,
            self::ARCHIVED => XeroContact::CONTACT_STATUS_ARCHIVED,
        };
    }
}
