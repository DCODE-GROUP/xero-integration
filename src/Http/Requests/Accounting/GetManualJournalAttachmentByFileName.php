<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getManualJournalAttachmentByFileName
 */
class GetManualJournalAttachmentByFileName extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/ManualJournals/{$this->manualJournalId}/Attachments/{$this->fileName}";
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function __construct(
        protected string $manualJournalId,
        protected string $fileName,
        protected string $contentType,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['contentType' => $this->contentType]);
    }
}
