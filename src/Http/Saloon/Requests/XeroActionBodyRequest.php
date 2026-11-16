<?php

namespace Dcodegroup\XeroIntegration\Http\Saloon\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

/**
 * An action request that carries a JSON body, such as creating or updating a timesheet line.
 */
class XeroActionBodyRequest extends XeroActionRequest implements HasBody
{
    use HasJsonBody;

    /**
     * @param  array<int|string, mixed>  $payload
     */
    public function __construct(
        Method $actionMethod,
        string $path,
        protected array $payload,
    ) {
        parent::__construct($actionMethod, $path);
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
