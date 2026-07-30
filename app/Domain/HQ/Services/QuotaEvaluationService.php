<?php

namespace App\Domain\HQ\Services;

use App\Models\HQTenant;
use App\Models\HQQuotaRule;
use App\Models\HQQuotaViolation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuotaEvaluationService
{
    public function __construct(
        protected HQAlertService $alertService,
        protected HQEntitlementService $entitlementService
    ) {}

    /**
     * Evaluate incoming metrics against quotas.
     */
    public function evaluate(HQTenant $tenant, array $metrics): void
    {
        $planLimits = $this->entitlementService->getLimits($tenant);
        
        // Also check if there are explicit overrides for this tenant
        $customRules = HQQuotaRule::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->get()
            ->keyBy('metric_key');

        foreach ($metrics as $metricKey => $currentUsage) {
            $criticalLimit = null;
            $warningLimit = null;

            if ($customRules->has($metricKey)) {
                $rule = $customRules->get($metricKey);
                $criticalLimit = $rule->critical_threshold;
                $warningLimit = $rule->warning_threshold;
            } elseif (isset($planLimits[$metricKey])) {
                $criticalLimit = (float) $planLimits[$metricKey];
                // Default warning at 80% if not explicitly set in custom rules
                $warningLimit = $criticalLimit * 0.8; 
            }

            if ($criticalLimit !== null) {
                $this->checkThresholds($tenant, $metricKey, (float)$currentUsage, $criticalLimit, $warningLimit);
            }
        }
    }

    /**
     * Check actual value against thresholds and generate violations/alerts.
     */
    protected function checkThresholds(HQTenant $tenant, string $metricKey, float $actual, float $critical, ?float $warning): void
    {
        $severity = null;

        if ($actual >= $critical) {
            $severity = 'critical';
        } elseif ($warning !== null && $actual >= $warning) {
            $severity = 'warning';
        }

        if ($severity) {
            $this->recordViolation($tenant, $metricKey, $actual, $severity === 'critical' ? $critical : $warning, $severity);
        } else {
            // If the value is now below thresholds, resolve any active open violations
            $this->resolveActiveViolations($tenant, $metricKey);
        }
    }

    /**
     * Record a violation and dispatch an alert if it's new.
     */
    protected function recordViolation(HQTenant $tenant, string $metricKey, float $actual, float $limit, string $severity): void
    {
        // Check if an unresolved violation already exists for this severity
        $existing = HQQuotaViolation::where('tenant_id', $tenant->id)
            ->where('metric_key', $metricKey)
            ->whereNull('resolved_at')
            ->orderByDesc('created_at')
            ->first();

        // If no existing, or existing is of a lower severity (e.g., was warning, now critical)
        if (!$existing || ($existing->severity === 'warning' && $severity === 'critical')) {
            // Resolve the lower severity one if upgrading
            if ($existing) {
                $existing->update(['resolved_at' => now()]);
            }

            $violation = HQQuotaViolation::create([
                'tenant_id' => $tenant->id,
                'metric_key' => $metricKey,
                'limit_value' => $limit,
                'actual_value' => $actual,
                'severity' => $severity,
            ]);

            // Dispatch alert via HQAlertService
            $title = ucfirst($severity) . " Quota Exceeded: " . ucfirst(str_replace('_', ' ', $metricKey));
            $message = "Tenant '{$tenant->name}' has reached {$actual} for {$metricKey} (Limit: {$limit}).";
            
            $this->alertService->createAlert(
                severity: $severity,
                title: $title,
                message: $message,
                tenantId: $tenant->id,
                metadata: [
                    'metric_key' => $metricKey,
                    'actual_value' => $actual,
                    'limit_value' => $limit,
                    'violation_id' => $violation->id,
                ]
            );

            // Write to Audit Trail
            app(HQAuditService::class)->logSystemAction(
                action: 'quota.violation',
                category: 'billing',
                severity: $severity,
                tenantId: $tenant->id,
                metadata: [
                    'metric_key' => $metricKey,
                    'actual' => $actual,
                    'limit' => $limit,
                ]
            );
        } else {
            // Just update the actual value to keep it current
            $existing->update(['actual_value' => $actual]);
        }
    }

    /**
     * Resolve active violations if usage drops back down.
     */
    protected function resolveActiveViolations(HQTenant $tenant, string $metricKey): void
    {
        HQQuotaViolation::where('tenant_id', $tenant->id)
            ->where('metric_key', $metricKey)
            ->whereNull('resolved_at')
            ->update(['resolved_at' => now()]);
    }
}
