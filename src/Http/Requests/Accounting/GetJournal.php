<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getJournal
 */
class GetJournal extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Journals/{$this->journalId}";
    }

    /**
     * @param  string  $journalId  Unique identifier for a Journal
     */
    public function __construct(
        protected string $journalId,
    ) {}
}
