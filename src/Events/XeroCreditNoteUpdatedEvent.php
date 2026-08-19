<?php

namespace Dcodegroup\XeroIntegration\Events;

use Dcodegroup\XeroIntegration\Data\XeroCreditNoteData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class XeroCreditNoteUpdatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroCreditNoteUpdatedEvent constructor.
     */
    public function __construct(public XeroCreditNoteData $data) {}
}
