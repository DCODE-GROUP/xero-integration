<?php

namespace Dcodegroup\XeroIntegration\Exceptions;

use Exception;

class XeroRateLimitExceededException extends Exception
{
    public static function exceeded(): self
    {
        return new self('Xero API rate limit exceeded. Please try again later.');
    }
}
