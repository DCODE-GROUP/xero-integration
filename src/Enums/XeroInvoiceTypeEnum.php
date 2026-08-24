<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Invoice as XeroInvoice;

enum XeroInvoiceTypeEnum: string
{
    case ACCPAY = 'accpay';
    case ACCREC = 'accrec';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACCPAY => 'Accounts Payable',
            self::ACCREC => 'Accounts Receivable',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::ACCPAY => XeroInvoice::INVOICE_TYPE_ACCPAY,
            self::ACCREC => XeroInvoice::INVOICE_TYPE_ACCREC,
        };
    }

    public static function fromXero(string $xeroValue)
    {
        return match ($xeroValue) {
            XeroInvoice::INVOICE_TYPE_ACCPAY => self::ACCPAY,
            XeroInvoice::INVOICE_TYPE_ACCREC => self::ACCREC,
            default => null
        };
    }
}
