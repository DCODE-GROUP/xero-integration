<?php

namespace DcodeGroup\XeroIntegration\Http\Requests\Accounting;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getCreditNote
 */
class GetCreditNote extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/CreditNotes/{$this->creditNoteId}";
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  null|int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     */
    public function __construct(
        protected string $creditNoteId,
        protected ?int $unitdp = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['unitdp' => $this->unitdp]);
    }
}
