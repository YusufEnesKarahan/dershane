<?php

namespace App\Core\Traits;

use App\Core\Scopes\BranchScope;

trait TenantScoped
{
    /**
     * The "boot" method of the trait.
     */
    protected static function bootTenantScoped(): void
    {
        static::addGlobalScope(new BranchScope);
    }
}
