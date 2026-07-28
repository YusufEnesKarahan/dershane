<?php

namespace App\Domain\HQ\Services;

use App\Models\HQSystemInstance;
use App\Models\HQCentralCommand;
use App\Models\HQApiConnection;
use Carbon\Carbon;

class HQMonitoringService
{
    public function getDashboardMetrics(): array
    {
        $now = Carbon::now();
        $offlineThreshold = $now->copy()->subMinutes(15);

        // Auto mark offline
        HQSystemInstance::where('status', 'online')
            ->where('last_seen_at', '<', $offlineThreshold)
            ->update(['status' => 'offline']);

        $totalSystems = HQSystemInstance::count();
        $onlineSystems = HQSystemInstance::where('status', 'online')->count();
        $offlineSystems = HQSystemInstance::where('status', 'offline')->count();
        $pendingCommands = HQCentralCommand::where('status', 'pending')->count();
        
        $lastCommunication = HQSystemInstance::max('last_seen_at');
        $failedConnections = HQApiConnection::where('status', 'failed')->where('created_at', '>=', now()->subDay())->count();

        return [
            'total_systems' => $totalSystems,
            'online_systems' => $onlineSystems,
            'offline_systems' => $offlineSystems,
            'pending_commands' => $pendingCommands,
            'last_communication' => $lastCommunication,
            'failed_connections' => $failedConnections,
        ];
    }
}
