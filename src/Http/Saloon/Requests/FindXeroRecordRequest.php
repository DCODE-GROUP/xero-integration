<?php

namespace Dcodegroup\XeroIntegration\Http\Saloon\Requests;

use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class FindXeroRecordRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected XeroEndpoint $endpoint,
        protected string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->endpoint->path([$this->id]);
    }
}
