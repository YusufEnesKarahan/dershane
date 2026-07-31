<?php

namespace App\Domain\HQ\Services\Governance;

use App\Models\HQRiskScore;
use App\Models\HQTenant;

class RiskEngineService
{
    /**
     * Calculate the risk score dynamically for a given tenant.
     * Higher score means higher risk.
     * 0-20 = Healthy, 20-50 = Warning, 50+ = Critical.
     */
    public function calculateRisk(HQTenant $tenant, array $eventsContext): HQRiskScore
    {
        $score = 0;
        $factors = [];

        // Example dynamic logic based on eventsContext
        if (isset($eventsContext['backup_failed']) && $eventsContext['backup_failed'] === true) {
            $score += 15;
            $factors[] = 'Backup failed (+15)';
        }

        if (isset($eventsContext['critical_alert']) && $eventsContext['critical_alert'] > 0) {
            $score += (20 * $eventsContext['critical_alert']);
            $factors[] = "Critical Alert(s): {$eventsContext['critical_alert']} (+".(20 * $eventsContext['critical_alert']).")";
        }

        if (isset($eventsContext['status']) && $eventsContext['status'] === 'offline') {
            $score += 25;
            $factors[] = 'Offline (+25)';
        }

        if (isset($eventsContext['license_expired']) && $eventsContext['license_expired'] === true) {
            $score += 30;
            $factors[] = 'Expired License (+30)';
        }

        // Determine Level
        if ($score >= 50) {
            $level = 'critical';
        } elseif ($score >= 20) {
            $level = 'warning';
        } else {
            $level = 'healthy';
        }

        $riskRecord = HQRiskScore::create([
            'tenant_id' => $tenant->id,
            'score' => $score,
            'level' => $level,
            'factors' => $factors,
            'calculated_at' => now(),
        ]);

        event(new \App\Events\RiskScoreChanged($tenant, $riskRecord));

        return $riskRecord;
    }
}
