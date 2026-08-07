<?php

namespace DcodeGroup\XeroIntegration\Enums;

enum XeroRelationshipsEnum: string
{
    case ADDRESS = 'address';
    case CONTACT = 'contact';
    case CONTACT_PERSON = 'contactPerson';
    case CREDIT_NOTE = 'creditNote';
    case INVOICE = 'invoice';
    case ITEM = 'item';
    case OVERPAYMENT = 'overpayment';
    case PAYMENT = 'payment';
    case PHONE = 'phone';
    case PREPAYMENT = 'prepayment';
    case QUOTE = 'quote';

    public function getModelClass(): string
    {
        return match ($this) {
            self::ADDRESS => 'XeroPHP\Models\Accounting\Address',
            self::CONTACT => 'XeroPHP\Models\Accounting\Contact',
            self::CONTACT_PERSON => 'XeroPHP\Models\Accounting\ContactPerson',
            self::CREDIT_NOTE => 'XeroPHP\Models\Accounting\CreditNote',
            self::INVOICE => 'XeroPHP\Models\Accounting\Invoice',
            self::ITEM => 'XeroPHP\Models\Accounting\LineItem',
            self::OVERPAYMENT => 'XeroPHP\Models\Accounting\Overpayment',
            self::PAYMENT => 'XeroPHP\Models\Accounting\Payment',
            self::PHONE => 'XeroPHP\Models\Accounting\Phone',
            self::PREPAYMENT => 'XeroPHP\Models\Accounting\Prepayment',
            self::QUOTE => 'XeroPHP\Models\Accounting\Quote',
        };
    }

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
