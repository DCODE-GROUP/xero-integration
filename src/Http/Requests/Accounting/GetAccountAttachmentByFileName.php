<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getAccountAttachmentByFileName
 */
class GetAccountAttachmentByFileName extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/Accounts/{$this->accountId}/Attachments/{$this->fileName}";
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function __construct(
        protected string $accountId,
        protected string $fileName,
        protected string $contentType,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['contentType' => $this->contentType]);
    }
}
