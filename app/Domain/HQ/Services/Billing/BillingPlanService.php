<?php

namespace App\Domain\HQ\Services\Billing;

use App\Models\HQPlan;

class BillingPlanService
{
    public function createPlan(array $data): HQPlan
    {
        return HQPlan::create($data);
    }

    public function updatePlan(HQPlan $plan, array $data): HQPlan
    {
        $plan->update($data);
        return $plan;
    }

    public function getActivePlans()
    {
        return HQPlan::where('status', 'active')->get();
    }
}
