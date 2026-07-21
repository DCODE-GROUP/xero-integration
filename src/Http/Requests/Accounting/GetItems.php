<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getItems
 */
class GetItems extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Items';
    }

    /**
     * @param  null|string  $where  Filter by an any element
     * @param  null|string  $order  Order by an any element
     * @param  null|int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  null|string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function __construct(
        protected ?string $where = null,
        protected ?string $order = null,
        protected ?int $unitdp = null,
        protected ?string $ifModifiedSince = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['where' => $this->where, 'order' => $this->order, 'unitdp' => $this->unitdp]);
    }

    public function defaultHeaders(): array
    {
        return array_filter(['If-Modified-Since' => $this->ifModifiedSince]);
    }
}
