<?php

namespace App\Domain\HQ\Services\Fleet;

use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Models\HQInstanceGroup;

class FleetService
{
    /**
     * Get statistics for the fleet overview dashboard.
     */
    public function getFleetOverview(): array
    {
        $totalInstances = HQSystemInstance::count();
        $onlineInstances = HQSystemInstance::where('status', 'online')->count();
        $offlineInstances = HQSystemInstance::where('status', 'offline')->count();
        
        $deployingTargets = \App\Models\HQDeploymentTarget::where('status', 'running')->count();
        $failedDeployments = \App\Models\HQDeploymentTarget::where('status', 'failed')->count();
        $rollbackDeployments = \App\Models\HQDeploymentTarget::where('status', 'rolled_back')->count();
        
        $activeMaintenance = \App\Models\HQMaintenanceWindow::where('status', 'active')->count();

        // Version distribution
        $versionDistribution = HQSystemInstance::select('system_version', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('system_version')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'system_version')
            ->toArray();

        return [
            'total_instances' => $totalInstances,
            'online' => $onlineInstances,
            'offline' => $offlineInstances,
            'deploying' => $deployingTargets,
            'failed' => $failedDeployments,
            'rollback' => $rollbackDeployments,
            'maintenance' => $activeMaintenance,
            'version_distribution' => $versionDistribution,
        ];
    }
}
