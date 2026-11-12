<?php

namespace Dcodegroup\XeroIntegration\Data\Contracts;

use Illuminate\Support\Collection;

/**
 * A data type that can fetch all of its records from Xero.
 */
interface ListsXeroRecords
{
    /**
     * @return Collection<int, self>
     */
    public static function list(): Collection;
}
