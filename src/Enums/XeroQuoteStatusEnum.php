<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Quote as XeroQuote;

enum XeroQuoteStatusEnum: string
{
    case DRAFT = 'draft';
    case DELETED = 'deleted';
    case SENT = 'sent';
    case DECLINED = 'declined';
    case ACCEPTED = 'accepted';
    case INVOICED = 'invoiced';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::DELETED => 'Deleted',
            self::DECLINED => 'Declined',
            self::ACCEPTED => 'Accepted',
            self::INVOICED => 'Invoiced',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::DRAFT => XeroQuote::QUOTE_STATUS_DRAFT,
            self::SENT => XeroQuote::QUOTE_STATUS_SENT,
            self::DELETED => XeroQuote::QUOTE_STATUS_DELETED,
            self::DECLINED => XeroQuote::QUOTE_STATUS_DECLINED,
            self::ACCEPTED => XeroQuote::QUOTE_STATUS_ACCEPTED,
            self::INVOICED => XeroQuote::QUOTE_STATUS_INVOICED,
        };
    }
}
