<?php

namespace App\Domain\Onboarding\Services;

use App\Models\HQOnboardingFlow;
use App\Models\HQTenant;
use App\Models\HQPlan;
use Illuminate\Support\Facades\Log;
use Exception;

class OnboardingService
{
    protected $provisioningService;

    public function __construct(TenantProvisioningService $provisioningService)
    {
        $this->provisioningService = $provisioningService;
    }

    public function startOnboarding(array $data): HQOnboardingFlow
    {
        $flow = HQOnboardingFlow::create([
            'current_step' => 'tenant_creation',
            'status' => 'in_progress',
            'metadata' => [
                'initial_data' => $data,
            ],
        ]);

        return $flow;
    }

    public function advanceStep(HQOnboardingFlow $flow, string $step, array $payload = [])
    {
        Log::info("OnboardingService: Advancing flow {$flow->uuid} to step {$step}");
        
        $flow->update([
            'current_step' => $step,
            'metadata' => array_merge($flow->metadata ?? [], [$step => $payload]),
        ]);

        // Orchestrate steps
        switch ($step) {
            case 'tenant_creation':
                $tenant = $this->provisioningService->createTenant($payload);
                $flow->update(['tenant_id' => $tenant->id]);
                break;
                
            case 'plan_selection':
                if (!$flow->tenant_id) throw new Exception("Tenant not created yet.");
                $plan = HQPlan::find($payload['plan_id']);
                $this->provisioningService->setupBilling(HQTenant::find($flow->tenant_id), $plan);
                break;
                
            case 'admin_creation':
                if (!$flow->tenant_id) throw new Exception("Tenant not created yet.");
                $this->provisioningService->createAdminUser(HQTenant::find($flow->tenant_id), $payload);
                break;

            case 'default_config':
                if (!$flow->tenant_id) throw new Exception("Tenant not created yet.");
                $this->provisioningService->createDefaultConfig(HQTenant::find($flow->tenant_id));
                break;

            case 'iam_setup':
                if (!$flow->tenant_id) throw new Exception("Tenant not created yet.");
                $this->provisioningService->setupIAM(HQTenant::find($flow->tenant_id));
                break;

            case 'portal_activation':
                if (!$flow->tenant_id) throw new Exception("Tenant not created yet.");
                $this->provisioningService->activatePortal(HQTenant::find($flow->tenant_id));
                break;
        }

        return $flow;
    }

    public function completeOnboarding(HQOnboardingFlow $flow)
    {
        \App\Jobs\FinalizeOnboardingJob::dispatch($flow->id);
    }
}
