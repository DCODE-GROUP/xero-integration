<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getLinkedTransactions
 */
class GetLinkedTransactions extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/LinkedTransactions';
    }

    /**
     * @param  null|int  $page  Up to 100 linked transactions will be returned in a single API call. Use the page parameter to specify the page to be returned e.g. page=1.
     * @param  null|string  $linkedTransactionId  The Xero identifier for an Linked Transaction
     * @param  null|string  $sourceTransactionId  Filter by the SourceTransactionID. Get the linked transactions created from a particular ACCPAY invoice
     * @param  null|string  $contactId  Filter by the ContactID. Get all the linked transactions that have been assigned to a particular customer.
     * @param  null|string  $status  Filter by the combination of ContactID and Status. Get  the linked transactions associated to a  customer and with a status
     * @param  null|string  $targetTransactionId  Filter by the TargetTransactionID. Get all the linked transactions allocated to a particular ACCREC invoice
     */
    public function __construct(
        protected ?int $page = null,
        protected ?string $linkedTransactionId = null,
        protected ?string $sourceTransactionId = null,
        protected ?string $contactId = null,
        protected ?string $status = null,
        protected ?string $targetTransactionId = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'page' => $this->page,
            'LinkedTransactionID' => $this->linkedTransactionId,
            'SourceTransactionID' => $this->sourceTransactionId,
            'ContactID' => $this->contactId,
            'Status' => $this->status,
            'TargetTransactionID' => $this->targetTransactionId,
        ]);
    }
}
