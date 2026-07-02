<?php

namespace DcodeGroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Payment as XeroPayment;

enum XeroPaymentTypesEnum: string
{
    case ACCOUNTS_RECEIVABLE_PAYMENT = 'accounts_receivable_payment';
    case ACCOUNTS_PAYABLE_PAYMENT = 'accounts_payable_payment';
    case ACCOUNTS_RECEIVABLE_CREDIT_PAYMENT = 'accounts_receivable_credit_payment';
    case ACCOUNTS_PAYABLE_CREDIT_PAYMENT = 'accounts_payable_credit_payment';
    case ACCOUNTS_RECEIVABLE_OVERPAYMENT_PAYMENT = 'accounts_receivable_overpayment_payment';
    case ACCOUNTS_RECEIVABLE_PREPAYMENT_PAYMENT = 'accounts_receivable_prepayment_payment';
    case ACCOUNTS_PAYABLE_PREPAYMENT_PAYMENT = 'accounts_payable_prepayment_payment';
    case ACCOUNTS_PAYABLE_OVERPAYMENT_PAYMENT = 'accounts_payable_overpayment_payment';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACCOUNTS_RECEIVABLE_PAYMENT => 'Accounts Receivable Payment',
            self::ACCOUNTS_PAYABLE_PAYMENT => 'Accounts Payable Payment',
            self::ACCOUNTS_RECEIVABLE_CREDIT_PAYMENT => 'Accounts Receivable Credit Payment',
            self::ACCOUNTS_PAYABLE_CREDIT_PAYMENT => 'Accounts Payable Credit Payment',
            self::ACCOUNTS_RECEIVABLE_OVERPAYMENT_PAYMENT => 'Accounts Receivable Overpayment Payment',
            self::ACCOUNTS_RECEIVABLE_PREPAYMENT_PAYMENT => 'Accounts Receivable Prepayment Payment',
            self::ACCOUNTS_PAYABLE_PREPAYMENT_PAYMENT => 'Accounts Payable Prepayment Payment',
            self::ACCOUNTS_PAYABLE_OVERPAYMENT_PAYMENT => 'Accounts Payable Overpayment Payment',
        };
    }

    public function getXeroValue(): string
    {
        return match ($this) {
            self::ACCOUNTS_RECEIVABLE_PAYMENT => XeroPayment::PAYMENT_TYPE_ACCRECPAYMENT,
            self::ACCOUNTS_PAYABLE_PAYMENT => XeroPayment::PAYMENT_TYPE_ACCPAYPAYMENT,
            self::ACCOUNTS_RECEIVABLE_CREDIT_PAYMENT => XeroPayment::PAYMENT_TYPE_ARCREDITPAYMENT,
            self::ACCOUNTS_PAYABLE_CREDIT_PAYMENT => XeroPayment::PAYMENT_TYPE_APCREDITPAYMENT,
            self::ACCOUNTS_RECEIVABLE_OVERPAYMENT_PAYMENT => XeroPayment::PAYMENT_TYPE_AROVERPAYMENTPAYMENT,
            self::ACCOUNTS_RECEIVABLE_PREPAYMENT_PAYMENT => XeroPayment::PAYMENT_TYPE_ARPREPAYMENTPAYMENT,
            self::ACCOUNTS_PAYABLE_PREPAYMENT_PAYMENT => XeroPayment::PAYMENT_TYPE_APPREPAYMENTPAYMENT,
            self::ACCOUNTS_PAYABLE_OVERPAYMENT_PAYMENT => XeroPayment::PAYMENT_TYPE_APOVERPAYMENTPAYMENT,
        };
    }
}
