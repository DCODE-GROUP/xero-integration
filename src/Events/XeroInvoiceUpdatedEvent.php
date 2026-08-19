<?php

namespace Dcodegroup\XeroIntegration\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use XeroPHP\Models\Accounting\Invoice as XeroInvoice;

class XeroInvoiceUpdatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroInvoiceUpdatedEvent constructor.
     */
    public function __construct(public XeroInvoice $xeroInvoice) {}
}
