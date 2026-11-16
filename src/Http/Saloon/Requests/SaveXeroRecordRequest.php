<?php

namespace Dcodegroup\XeroIntegration\Http\Saloon\Requests;

use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class SaveXeroRecordRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<string, mixed>  $record
     * @param  string|null  $id  Updates the record with this id; creates a new one when null.
     */
    public function __construct(
        protected XeroEndpoint $endpoint,
        protected array $record,
        protected ?string $id = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->endpoint->path($this->id === null ? [] : [$this->id]);
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->endpoint->api->wrapRecord($this->endpoint->resource, $this->record);
    }
}
