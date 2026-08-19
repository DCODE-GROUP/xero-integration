<?php

namespace Dcodegroup\XeroIntegration\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use XeroPHP\Models\Accounting\Invoice as XeroInvoice;

class XeroInvoiceCreatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroInvoiceCreatedEvent constructor.
     */
    public function __construct(public XeroInvoice $xeroInvoice) {}
}
