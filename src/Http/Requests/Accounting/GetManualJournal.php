<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getManualJournal
 */
class GetManualJournal extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/ManualJournals/{$this->manualJournalId}";
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     */
    public function __construct(
        protected string $manualJournalId,
    ) {}
}
