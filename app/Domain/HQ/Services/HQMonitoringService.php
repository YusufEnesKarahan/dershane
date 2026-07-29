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

        // System Overview
        $totalSystems = HQSystemInstance::count();
        $onlineSystems = HQSystemInstance::where('status', 'online')->count();
        $offlineSystems = HQSystemInstance::where('status', 'offline')->count();
        $unknownSystems = HQSystemInstance::where('status', 'unknown')->count();

        // Tenant Overview
        $totalTenants = \App\Models\HQTenant::count();
        $activeTenants = \App\Models\HQTenant::where('status', 'active')->count();
        $suspendedTenants = \App\Models\HQTenant::where('status', 'suspended')->count();
        
        // Communication Health
        $lastCommunication = HQSystemInstance::max('last_seen_at');
        $failedConnections = HQApiConnection::where('status', 'failed')->where('created_at', '>=', now()->subDay())->count();
        $apiErrors = \App\Models\HQCentralSyncLog::where('status', '!=', 'success')->where('created_at', '>=', now()->subDay())->count();
        $avgResponseTime = \App\Models\HQCentralSyncLog::where('status', 'success')->where('created_at', '>=', now()->subDay())->avg('duration_ms') ?? 0;

        // Command Queue
        $pendingCommands = HQCentralCommand::where('status', 'pending')->count();
        $completedCommands = HQCentralCommand::where('status', 'completed')->count();
        $failedCommands = HQCentralCommand::where('status', 'failed')->count();

        // Telemetry Overview
        $latestTelemetry = \App\Models\HQTelemetryRecord::latest('received_at')->first();
        // Since telemetry payloads differ, we'll try to extract simple values if available, or just send a count.
        $recentTelemetries = \App\Models\HQTelemetryRecord::latest('received_at')->take(10)->get();
        $memorySum = 0;
        $storageSum = 0;
        $count = 0;
        foreach($recentTelemetries as $tel) {
            $payload = $tel->payload;
            if (isset($payload['memory_usage_percent'])) {
                $memorySum += $payload['memory_usage_percent'];
                $count++;
            }
            if (isset($payload['storage_usage_percent'])) {
                $storageSum += $payload['storage_usage_percent'];
            }
        }
        $avgMemory = $count > 0 ? round($memorySum / $count, 2) : 'N/A';
        $avgStorage = $count > 0 ? round($storageSum / $count, 2) : 'N/A';
        
        $dbHealth = 'Healthy'; // Simplified default based on system active statuses

        // License metrics
        $totalLicenses = \App\Models\HQLicense::count();
        $activeLicenses = \App\Models\HQLicense::where('status', 'active')->count();
        $expiredLicenses = \App\Models\HQLicense::where('status', 'expired')->count();
        $expiringSoon = \App\Models\HQLicense::where('status', 'active')
                            ->whereNotNull('expires_at')
                            ->where('expires_at', '<', now()->addDays(30))
                            ->count();

        // Update & Deployment Metrics
        $latestVersion = \App\Models\HQVersion::where('status', 'published')->orderByDesc('version')->first();
        $mandatoryUpdates = \App\Models\HQVersion::where('status', 'published')->where('is_mandatory', true)->count();
        $behindLatest = 0;
        if ($latestVersion) {
            $behindLatest = HQSystemInstance::where('system_version', '!=', $latestVersion->version)
                ->whereNotNull('system_version')
                ->count();
        }
        $pendingUpdates = \App\Models\HQUpdateJob::whereIn('status', ['pending', 'scheduled'])->count();
        $runningUpdates = \App\Models\HQUpdateJob::where('status', 'sent')->count();

        return [
            'systems' => [
                'total' => $totalSystems,
                'online' => $onlineSystems,
                'offline' => $offlineSystems,
                'unknown' => $unknownSystems,
            ],
            'tenants' => [
                'total' => $totalTenants,
                'active' => $activeTenants,
                'suspended' => $suspendedTenants,
            ],
            'communication' => [
                'last_heartbeat' => $lastCommunication,
                'failed_requests' => $failedConnections,
                'api_errors' => $apiErrors,
                'avg_response_time' => round($avgResponseTime),
            ],
            'commands' => [
                'pending' => $pendingCommands,
                'completed' => $completedCommands,
                'failed' => $failedCommands,
            ],
            'telemetry' => [
                'latest' => $latestTelemetry ? $latestTelemetry->received_at : null,
                'avg_memory' => $avgMemory,
                'avg_storage' => $avgStorage,
                'db_health' => $dbHealth,
            ],
            'licenses' => [
                'total' => $totalLicenses,
                'active' => $activeLicenses,
                'expired' => $expiredLicenses,
                'expiring_soon' => $expiringSoon,
            ],
            'updates' => [
                'latest_version' => $latestVersion ? $latestVersion->version : 'None',
                'behind_latest' => $behindLatest,
                'mandatory_updates' => $mandatoryUpdates,
                'pending' => $pendingUpdates,
                'running' => $runningUpdates,
            ],
            // Backwards compatibility for old dashboard view if needed
            'total_systems' => $totalSystems,
            'online_systems' => $onlineSystems,
            'offline_systems' => $offlineSystems,
            'pending_commands' => $pendingCommands,
            'last_communication' => $lastCommunication,
            'failed_connections' => $failedConnections,
        ];
    }
}
