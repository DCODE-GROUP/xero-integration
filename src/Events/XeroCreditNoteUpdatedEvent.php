<?php

namespace DcodeGroup\XeroIntegration\Events;

use DcodeGroup\XeroIntegration\Data\XeroCreditNoteData;
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
