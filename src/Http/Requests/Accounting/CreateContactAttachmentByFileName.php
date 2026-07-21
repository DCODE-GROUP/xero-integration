<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * createContactAttachmentByFileName
 */
class CreateContactAttachmentByFileName extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return "/Contacts/{$this->contactId}/Attachments/{$this->fileName}";
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     * @param  string  $fileName  Name of the attachment
     * @param  null|string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function __construct(
        protected string $contactId,
        protected string $fileName,
        protected ?string $idempotencyKey = null,
    ) {}

    public function defaultHeaders(): array
    {
        return array_filter(['Idempotency-Key' => $this->idempotencyKey]);
    }
}
