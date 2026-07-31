<?php

namespace App\Domain\HQ\Services\Billing;

use App\Models\HQTenant;
use App\Models\HQUsageRecord;
use App\Events\UsageLimitExceeded;
use App\Domain\HQ\Services\HQAlertService;

class UsageMeteringService
{
    protected $entitlementService;
    protected $alertService;

    public function __construct(EntitlementService $entitlementService, HQAlertService $alertService)
    {
        $this->entitlementService = $entitlementService;
        $this->alertService = $alertService;
    }

    public function recordUsage(HQTenant $tenant, string $metricName, float $value, string $period = null)
    {
        $period = $period ?? now()->format('Y-m');

        $usage = HQUsageRecord::firstOrCreate(
            ['tenant_id' => $tenant->id, 'metric_name' => $metricName, 'period' => $period],
            ['value' => 0]
        );

        $usage->value += $value;
        $usage->save();

        $this->checkLimitBreach($tenant, $metricName, $usage->value);

        return $usage;
    }

    public function checkLimitBreach(HQTenant $tenant, string $metricName, float $currentUsage)
    {
        $limit = $this->entitlementService->getLimit($tenant, $metricName);

        if ($limit !== null && is_numeric($limit)) {
            if ($currentUsage > (float) $limit) {
                event(new UsageLimitExceeded($tenant, $metricName));

                $this->alertService->createAlert(
                    severity: 'warning',
                    title: 'usage.limit.exceeded',
                    message: "Tenant {$tenant->id} exceeded limit for {$metricName}. Limit: {$limit}, Usage: {$currentUsage}.",
                    metadata: ['tenant_id' => $tenant->id, 'metric' => $metricName]
                );
            }
        }
    }
}
