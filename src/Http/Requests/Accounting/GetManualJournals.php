<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getManualJournals
 */
class GetManualJournals extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/ManualJournals';
    }

    /**
     * @param  null|string  $where  Filter by an any element
     * @param  null|string  $order  Order by an any element
     * @param  null|int  $page  e.g. page=1 – Up to 100 manual journals will be returned in a single API call with line items shown for each overpayment
     * @param  null|int  $pageSize  Number of records to retrieve per page
     * @param  null|string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function __construct(
        protected ?string $where = null,
        protected ?string $order = null,
        protected ?int $page = null,
        protected ?int $pageSize = null,
        protected ?string $ifModifiedSince = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['where' => $this->where, 'order' => $this->order, 'page' => $this->page, 'pageSize' => $this->pageSize]);
    }

    public function defaultHeaders(): array
    {
        return array_filter(['If-Modified-Since' => $this->ifModifiedSince]);
    }
}
