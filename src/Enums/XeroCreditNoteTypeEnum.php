<?php

namespace DcodeGroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\CreditNote as XeroCreditNote;

enum XeroCreditNoteTypeEnum: string
{
    case ACCPAYCREDIT = 'accpay_credit';
    case ACCRECCREDIT = 'accrec_credit';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACCPAYCREDIT => 'Accounts Payable Credit',
            self::ACCRECCREDIT => 'Accounts Receivable Credit',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::ACCPAYCREDIT => XeroCreditNote::CREDIT_NOTE_TYPE_ACCPAYCREDIT,
            self::ACCRECCREDIT => XeroCreditNote::CREDIT_NOTE_TYPE_ACCRECCREDIT,
        };
    }
}
