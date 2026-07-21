<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getPrepayments
 */
class GetPrepayments extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Prepayments';
    }

    /**
     * @param  null|string  $where  Filter by an any element
     * @param  null|string  $order  Order by an any element
     * @param  null|int  $page  e.g. page=1 – Up to 100 prepayments will be returned in a single API call with line items shown for each overpayment
     * @param  null|int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  null|int  $pageSize  Number of records to retrieve per page
     * @param  null|array  $invoiceNumbers  Filter by a comma-separated list of InvoiceNumbers
     * @param  null|string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function __construct(
        protected ?string $where = null,
        protected ?string $order = null,
        protected ?int $page = null,
        protected ?int $unitdp = null,
        protected ?int $pageSize = null,
        protected ?array $invoiceNumbers = null,
        protected ?string $ifModifiedSince = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'where' => $this->where,
            'order' => $this->order,
            'page' => $this->page,
            'unitdp' => $this->unitdp,
            'pageSize' => $this->pageSize,
            'InvoiceNumbers' => $this->invoiceNumbers,
        ]);
    }

    public function defaultHeaders(): array
    {
        return array_filter(['If-Modified-Since' => $this->ifModifiedSince]);
    }
}
