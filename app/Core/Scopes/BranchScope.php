<?php

namespace App\Core\Scopes;

use App\Core\Context\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Don't apply in console (unless we specifically want to via job middleware)
        // Or we could check if a context is explicitly set.
        $branchId = TenantContext::getActiveBranchId();

        if ($branchId) {
            $builder->where($model->getTable() . '.branch_id', $branchId);
        }
    }
}
