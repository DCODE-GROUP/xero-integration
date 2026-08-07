<?php

namespace DcodeGroup\XeroIntegration\Events;

use DcodeGroup\XeroIntegration\Data\XeroCreditNoteData;
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
