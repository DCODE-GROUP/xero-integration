<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getQuoteAttachmentByFileName
 */
class GetQuoteAttachmentByFileName extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/Quotes/{$this->quoteId}/Attachments/{$this->fileName}";
	}


	/**
	 * @param string $quoteId Unique identifier for an Quote
	 * @param string $fileName Name of the attachment
	 * @param string $contentType The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
	 */
	public function __construct(
		protected string $quoteId,
		protected string $fileName,
		protected string $contentType,
	) {
	}


	public function defaultHeaders(): array
	{
		return array_filter(['contentType' => $this->contentType]);
	}
}
