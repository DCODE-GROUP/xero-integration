<?php

namespace Dcodegroup\XeroIntegration\Events;

use Dcodegroup\XeroIntegration\Data\XeroInvoiceData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class XeroInvoiceUpdatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroInvoiceUpdatedEvent constructor.
     */
    public function __construct(public XeroInvoiceData $data) {}
}
