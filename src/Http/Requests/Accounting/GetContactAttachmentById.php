<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getContactAttachmentById
 */
class GetContactAttachmentById extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Contacts/{$this->contactId}/Attachments/{$this->attachmentId}";
	}


	/**
	 * @param string $contactId Unique identifier for a Contact
	 * @param string $attachmentId Unique identifier for Attachment object
	 * @param string $contentType The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
	 */
	public function __construct(
		protected string $contactId,
		protected string $attachmentId,
		protected string $contentType,
	) {
	}


	public function defaultHeaders(): array
	{
		return array_filter(['contentType' => $this->contentType]);
	}
}
