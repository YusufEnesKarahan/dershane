<?php

namespace App\Domain\HQ\Services\Governance;

use App\Models\HQComplianceFramework;
use App\Models\HQComplianceControl;
use App\Models\HQComplianceResult;
use App\Models\HQTenant;

class ComplianceService
{
    /**
     * Create a new compliance framework dynamically.
     */
    public function createFramework(array $data): HQComplianceFramework
    {
        return HQComplianceFramework::create($data);
    }

    /**
     * Add a control to a framework.
     */
    public function addControl(HQComplianceFramework $framework, array $controlData): HQComplianceControl
    {
        $controlData['framework_id'] = $framework->id;
        return HQComplianceControl::create($controlData);
    }

    /**
     * Calculate compliance score for a tenant against a specific framework.
     */
    public function evaluateTenantCompliance(HQTenant $tenant, HQComplianceFramework $framework, array $context): HQComplianceResult
    {
        $controls = $framework->controls()->with('policy')->get();
        $totalControls = $controls->count();
        
        if ($totalControls === 0) {
            return $this->recordResult($tenant, $framework, 100, []);
        }

        $passedCount = 0;
        $details = [];
        $policyEngine = app(PolicyEngineService::class);

        foreach ($controls as $control) {
            $isPassed = true; // Assume pass if no policy linked
            
            if ($control->policy) {
                $isPassed = $policyEngine->evaluate($control->policy, $context);
            }

            if ($isPassed) {
                $passedCount++;
            }

            $details[$control->control_code] = [
                'status' => $isPassed ? 'passed' : 'failed',
                'policy_id' => $control->policy_id,
            ];
        }

        $score = ($passedCount / $totalControls) * 100;

        $result = $this->recordResult($tenant, $framework, $score, $details);

        if ($score === 100) {
            event(new \App\Events\CompliancePassed($tenant, $framework, $score));
        } else {
            event(new \App\Events\ComplianceFailed($tenant, $framework, $score, $details));
        }

        return $result;
    }

    protected function recordResult(HQTenant $tenant, HQComplianceFramework $framework, float $score, array $details): HQComplianceResult
    {
        return HQComplianceResult::create([
            'tenant_id' => $tenant->id,
            'framework_id' => $framework->id,
            'score_percentage' => $score,
            'details' => $details,
            'evaluated_at' => now(),
        ]);
    }
}
