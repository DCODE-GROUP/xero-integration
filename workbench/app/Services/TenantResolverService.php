<?php

namespace Workbench\App\Services;

use Workbench\App\Models\Tenant;

class TenantResolverService
{
    public static function getTenant(): ?Tenant
    {
        return Tenant::factory()->create();
    }
}
