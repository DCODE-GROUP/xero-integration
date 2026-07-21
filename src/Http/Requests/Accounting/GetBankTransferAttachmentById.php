<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getBankTransferAttachmentById
 */
class GetBankTransferAttachmentById extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/BankTransfers/{$this->bankTransferId}/Attachments/{$this->attachmentId}";
	}


	/**
	 * @param string $bankTransferId Xero generated unique identifier for a bank transfer
	 * @param string $attachmentId Unique identifier for Attachment object
	 * @param string $contentType The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
	 */
	public function __construct(
		protected string $bankTransferId,
		protected string $attachmentId,
		protected string $contentType,
	) {
	}


	public function defaultHeaders(): array
	{
		return array_filter(['contentType' => $this->contentType]);
	}
}
