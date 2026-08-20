<?php

namespace Dcodegroup\XeroIntegration\Enums;

use XeroPHP\Models\Accounting\Address;
use XeroPHP\Models\Accounting\Contact;
use XeroPHP\Models\Accounting\Contact\ContactPerson;
use XeroPHP\Models\Accounting\CreditNote;
use XeroPHP\Models\Accounting\Invoice;
use XeroPHP\Models\Accounting\LineItem;
use XeroPHP\Models\Accounting\Overpayment;
use XeroPHP\Models\Accounting\Payment;
use XeroPHP\Models\Accounting\Phone;
use XeroPHP\Models\Accounting\Prepayment;
use XeroPHP\Models\Accounting\Quote;
use XeroPHP\Models\Accounting\TaxRate;
use XeroPHP\Models\PayrollAU\LeaveApplication as LeaveApplicationAU;
use XeroPHP\Models\PayrollAU\Timesheet as TimesheetAU;
use XeroPHP\Models\PayrollAU\Timesheet\TimesheetLine as TimesheetLineAU;

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
    case TIMESHEET_AU = 'timesheet_au';
    case TIMESHEET_LINE_AU = 'timesheet_line_au';
    case TAX_RATE = 'taxRate';
    case LEAVE_APPLICATION_AU = 'leave_application_au';

    public function getModelClass(): string
    {
        return match ($this) {
            self::ADDRESS => Address::class,
            self::CONTACT => Contact::class,
            self::CONTACT_PERSON => ContactPerson::class,
            self::CREDIT_NOTE => CreditNote::class,
            self::INVOICE => Invoice::class,
            self::ITEM => LineItem::class,
            self::OVERPAYMENT => Overpayment::class,
            self::PAYMENT => Payment::class,
            self::PHONE => Phone::class,
            self::PREPAYMENT => Prepayment::class,
            self::QUOTE => Quote::class,
            self::TIMESHEET_AU => TimesheetAU::class,
            self::TIMESHEET_LINE_AU => TimesheetLineAU::class,
            self::LEAVE_APPLICATION_AU => LeaveApplicationAU::class,
            self::TAX_RATE => TaxRate::class,
        };
    }

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
