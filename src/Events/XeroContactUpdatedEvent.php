<?php

namespace Dcodegroup\XeroIntegration\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use XeroPHP\Models\Accounting\Contact as XeroContact;

class XeroContactUpdatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroContactUpdatedEvent constructor.
     */
    public function __construct(public XeroContact $xeroContact) {}
}
