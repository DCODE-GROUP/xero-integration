<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Phone as XeroPhone;

enum XeroPhoneTypeEnum: string
{
    case DEFAULT = 'default';
    case DDI = 'ddi';
    case MOBILE = 'mobile';
    case FAX = 'fax';

    public function getLabel(): string
    {
        return match ($this) {
            self::DEFAULT => 'Default',
            self::DDI => 'Direct Dial In',
            self::MOBILE => 'Mobile',
            self::FAX => 'Fax',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::DEFAULT => XeroPhone::PHONE_TYPE_DEFAULT,
            self::DDI => XeroPhone::PHONE_TYPE_DDI,
            self::MOBILE => XeroPhone::PHONE_TYPE_MOBILE,
            self::FAX => XeroPhone::PHONE_TYPE_FAX,
        };
    }
}
