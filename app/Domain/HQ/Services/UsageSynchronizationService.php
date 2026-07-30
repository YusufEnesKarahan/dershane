<?php

namespace App\Domain\HQ\Services;

use App\Models\HQTenant;
use Illuminate\Support\Facades\Log;

class UsageSynchronizationService
{
    public function __construct(
        protected HQUsageService $usageService,
        protected QuotaEvaluationService $quotaEvaluationService
    ) {}

    /**
     * Process incoming telemetry and report usage securely.
     */
    public function processIncomingReport(HQTenant $tenant, array $payload): array
    {
        if (empty($payload['metrics']) || !is_array($payload['metrics'])) {
            return [
                'status' => 'error',
                'message' => 'Invalid or missing metrics payload.'
            ];
        }

        $metrics = $payload['metrics'];
        
        try {
            $this->usageService->recordMetrics($tenant, $metrics);
            
            // Generate a response with current quotas for the tenant so the ERP knows what limits to enforce locally
            $limits = app(HQEntitlementService::class)->getLimits($tenant);
            
            return [
                'status' => 'success',
                'message' => 'Usage recorded successfully.',
                'quotas' => $limits,
            ];
        } catch (\Exception $e) {
            Log::error("Failed to process usage report for tenant {$tenant->id}: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Internal Server Error'
            ];
        }
    }
}
