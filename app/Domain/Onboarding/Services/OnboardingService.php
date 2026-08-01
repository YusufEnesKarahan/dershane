<?php

namespace App\Domain\Onboarding\Services;

use App\Models\InstitutionRegistration;
use App\Models\Institution;
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

    public function startOnboarding(array $data): InstitutionRegistration
    {
        $flow = InstitutionRegistration::create([
            'current_step' => 'tenant_creation',
            'status' => 'in_progress',
            'metadata' => [
                'initial_data' => $data,
            ],
        ]);

        return $flow;
    }

    public function advanceStep(InstitutionRegistration $flow, string $step, array $payload = [])
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
                $this->provisioningService->setupBilling(Institution::find($flow->tenant_id), $plan);
                break;
                
            case 'admin_creation':
                if (!$flow->tenant_id) throw new Exception("Tenant not created yet.");
                $this->provisioningService->createAdminUser(Institution::find($flow->tenant_id), $payload);
                break;

            case 'default_config':
                if (!$flow->tenant_id) throw new Exception("Tenant not created yet.");
                $this->provisioningService->createDefaultConfig(Institution::find($flow->tenant_id));
                break;

            case 'iam_setup':
                if (!$flow->tenant_id) throw new Exception("Tenant not created yet.");
                $this->provisioningService->setupIAM(Institution::find($flow->tenant_id));
                break;

            case 'portal_activation':
                if (!$flow->tenant_id) throw new Exception("Tenant not created yet.");
                $this->provisioningService->activatePortal(Institution::find($flow->tenant_id));
                break;
        }

        return $flow;
    }

    public function completeOnboarding(InstitutionRegistration $flow)
    {
        \App\Jobs\FinalizeOnboardingJob::dispatch($flow->id);
    }
}
