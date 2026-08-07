<?php

namespace Dcodegroup\XeroIntegration\Events;

use Dcodegroup\XeroIntegration\Data\XeroCreditNoteData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class XeroCreditNoteCreatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * XeroCreditNoteCreatedEvent constructor.
     */
    public function __construct(public XeroCreditNoteData $data) {}
}
