<?php

namespace Dcodegroup\XeroIntegration\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use XeroPHP\Models\Accounting\Contact as XeroContact;

class XeroContactCreatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroContactCreatedEvent constructor.
     */
    public function __construct(public XeroContact $xeroContact) {}
}
