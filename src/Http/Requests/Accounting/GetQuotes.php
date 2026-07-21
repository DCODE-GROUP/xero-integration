<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getQuotes
 */
class GetQuotes extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Quotes';
    }

    /**
     * @param  null|string  $dateFrom  Filter for quotes after a particular date
     * @param  null|string  $dateTo  Filter for quotes before a particular date
     * @param  null|string  $expiryDateFrom  Filter for quotes expiring after a particular date
     * @param  null|string  $expiryDateTo  Filter for quotes before a particular date
     * @param  null|string  $contactId  Filter for quotes belonging to a particular contact
     * @param  null|string  $status  Filter for quotes of a particular Status
     * @param  null|int  $page  e.g. page=1 – Up to 100 Quotes will be returned in a single API call with line items shown for each quote
     * @param  null|string  $order  Order by an any element
     * @param  null|string  $quoteNumber  Filter by quote number (e.g. GET https://.../Quotes?QuoteNumber=QU-0001)
     * @param  null|string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function __construct(
        protected ?string $dateFrom = null,
        protected ?string $dateTo = null,
        protected ?string $expiryDateFrom = null,
        protected ?string $expiryDateTo = null,
        protected ?string $contactId = null,
        protected ?string $status = null,
        protected ?int $page = null,
        protected ?string $order = null,
        protected ?string $quoteNumber = null,
        protected ?string $ifModifiedSince = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'DateFrom' => $this->dateFrom,
            'DateTo' => $this->dateTo,
            'ExpiryDateFrom' => $this->expiryDateFrom,
            'ExpiryDateTo' => $this->expiryDateTo,
            'ContactID' => $this->contactId,
            'Status' => $this->status,
            'page' => $this->page,
            'order' => $this->order,
            'QuoteNumber' => $this->quoteNumber,
        ]);
    }

    public function defaultHeaders(): array
    {
        return array_filter(['If-Modified-Since' => $this->ifModifiedSince]);
    }
}
