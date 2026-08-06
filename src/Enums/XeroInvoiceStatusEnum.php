<?php

namespace DcodeGroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Invoice as XeroInvoice;

enum XeroInvoiceStatusEnum: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case DELETED = 'deleted';
    case AUTHORISED = 'authorised';
    case PAID = 'paid';
    case VOIDED = 'voided';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::DELETED => 'Deleted',
            self::AUTHORISED => 'Authorised',
            self::PAID => 'Paid',
            self::VOIDED => 'Voided',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::DRAFT => XeroInvoice::INVOICE_STATUS_DRAFT,
            self::SUBMITTED => XeroInvoice::INVOICE_STATUS_SUBMITTED,
            self::DELETED => XeroInvoice::INVOICE_STATUS_DELETED,
            self::AUTHORISED => XeroInvoice::INVOICE_STATUS_AUTHORISED,
            self::PAID => XeroInvoice::INVOICE_STATUS_PAID,
            self::VOIDED => XeroInvoice::INVOICE_STATUS_VOIDED,
        };
    }
}
