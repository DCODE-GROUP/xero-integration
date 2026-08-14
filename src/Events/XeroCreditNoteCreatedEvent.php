<?php

namespace Dcodegroup\XeroIntegration\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use XeroPHP\Models\Accounting\CreditNote as XeroCreditNote;

class XeroCreditNoteCreatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroCreditNoteCreatedEvent constructor.
     */
    public function __construct(public XeroCreditNote $xeroCreditNote) {}
}
