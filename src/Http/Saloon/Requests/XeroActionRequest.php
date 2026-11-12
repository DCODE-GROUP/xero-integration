<?php

namespace Dcodegroup\XeroIntegration\Http\Saloon\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * A request without a body to a sub-resource or action of a record, such as `/Timesheets/{id}/Approve`.
 */
class XeroActionRequest extends Request
{
    public function __construct(
        protected Method $actionMethod,
        protected string $path,
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->path;
    }

    public function getMethod(): Method
    {
        return $this->actionMethod;
    }
}
