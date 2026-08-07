<?php

namespace DcodeGroup\XeroIntegration\Events;

use DcodeGroup\XeroIntegration\Data\XeroInvoiceData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class XeroInvoiceCreatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroInvoiceCreatedEvent constructor.
     */
    public function __construct(public XeroInvoiceData $data) {}
}
