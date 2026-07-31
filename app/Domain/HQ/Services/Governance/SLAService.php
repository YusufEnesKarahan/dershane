<?php

namespace App\Domain\HQ\Services\Governance;

use App\Models\HQSlaPolicy;
use App\Models\HQSlaViolation;
use App\Models\HQTenant;

class SLAService
{
    /**
     * Check SLAs against current metric telemetry.
     */
    public function checkSLA(HQSlaPolicy $slaPolicy, HQTenant $tenant, $actualValue): void
    {
        $isViolated = false;

        $threshold = $slaPolicy->threshold_value;

        switch ($slaPolicy->operator) {
            case '==':
                $isViolated = ($actualValue == $threshold);
                break;
            case '>':
                $isViolated = ((float) $actualValue > (float) $threshold);
                break;
            case '<':
                $isViolated = ((float) $actualValue < (float) $threshold);
                break;
            case '>=':
                $isViolated = ((float) $actualValue >= (float) $threshold);
                break;
            case '<=':
                $isViolated = ((float) $actualValue <= (float) $threshold);
                break;
            case '!=':
                $isViolated = ($actualValue != $threshold);
                break;
        }

        if ($isViolated) {
            $this->triggerViolation($slaPolicy, $tenant, $actualValue);
        }
    }

    protected function triggerViolation(HQSlaPolicy $slaPolicy, HQTenant $tenant, $actualValue): void
    {
        $violation = HQSlaViolation::create([
            'sla_policy_id' => $slaPolicy->id,
            'tenant_id' => $tenant->id,
            'actual_value' => (string) $actualValue,
            'status' => 'open',
            'metadata' => ['threshold' => $slaPolicy->threshold_value, 'operator' => $slaPolicy->operator],
        ]);

        event(new \App\Events\SLAViolationDetected($violation));
    }
}
