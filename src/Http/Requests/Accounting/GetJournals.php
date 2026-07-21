<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getJournals
 */
class GetJournals extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Journals';
    }

    /**
     * @param  null|int  $offset  Offset by a specified journal number. e.g. journals with a JournalNumber greater than the offset will be returned
     * @param  null|bool  $paymentsOnly  Filter to retrieve journals on a cash basis. Journals are returned on an accrual basis by default.
     * @param  null|string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function __construct(
        protected ?int $offset = null,
        protected ?bool $paymentsOnly = null,
        protected ?string $ifModifiedSince = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['offset' => $this->offset, 'paymentsOnly' => $this->paymentsOnly]);
    }

    public function defaultHeaders(): array
    {
        return array_filter(['If-Modified-Since' => $this->ifModifiedSince]);
    }
}
