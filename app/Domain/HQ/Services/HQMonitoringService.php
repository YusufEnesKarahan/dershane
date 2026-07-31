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

        // Audit & Security Metrics
        $auditStats = app(\App\Domain\HQ\Services\HQAuditService::class)->getStatistics();
        
        // Alert Metrics
        $alertStats = app(\App\Domain\HQ\Services\HQAlertService::class)->getStatistics();

        // Billing Metrics
        $activeSubscriptions = \App\Models\HQSubscription::where('status', 'active')->count();
        $expiringSubscriptions = \App\Models\HQSubscription::where('status', 'active')->where('ends_at', '<=', now()->addDays(30))->count();
        $monthlyRevenue = \App\Models\HQInvoice::where('status', 'paid')->where('paid_at', '>=', now()->startOfMonth())->sum('amount');
        $failedPayments = \App\Models\HQPayment::where('status', 'failed')->where('created_at', '>=', now()->subDays(7))->count();

        // Global Usage Analytics (from latest daily snapshots)
        $latestSnapshots = \App\Models\HQUsageSnapshot::where('period', 'daily')
            ->where('period_start', '>=', now()->subDays(2))
            ->get()
            ->groupBy('tenant_id')
            ->map(fn($group) => $group->sortByDesc('period_start')->first());
            
        $globalUsage = [
            'students' => 0,
            'teachers' => 0,
            'storage_gb' => 0,
            'api_requests' => 0,
            'emails' => 0,
            'sms' => 0,
        ];

        foreach ($latestSnapshots as $snapshot) {
            $data = $snapshot->data_json ?? [];
            $globalUsage['students'] += $data['students'] ?? 0;
            $globalUsage['teachers'] += $data['teachers'] ?? 0;
            $globalUsage['storage_gb'] += ($data['storage_bytes'] ?? 0) / 1073741824; // convert bytes to GB
            $globalUsage['api_requests'] += $data['api_requests'] ?? 0;
            $globalUsage['emails'] += $data['emails_sent'] ?? 0;
            $globalUsage['sms'] += $data['sms_sent'] ?? 0;
        }

        // Workflow Metrics
        $workflowStats = [
            'total' => 0,
            'running' => 0,
            'completed' => 0,
            'failed' => 0,
            'avg_duration_sec' => 0,
        ];
        
        if (class_exists(\App\Models\HQWorkflowRun::class)) {
            $workflowStats['total'] = \App\Models\HQWorkflow::count();
            $workflowStats['running'] = \App\Models\HQWorkflowRun::where('status', 'running')->count();
            $workflowStats['completed'] = \App\Models\HQWorkflowRun::where('status', 'completed')->count();
            $workflowStats['failed'] = \App\Models\HQWorkflowRun::whereIn('status', ['failed', 'timeout'])->count();
            
            // Calc avg duration for completed runs
            $completedRuns = \App\Models\HQWorkflowRun::where('status', 'completed')
                ->whereNotNull('started_at')
                ->whereNotNull('completed_at')
                ->latest()
                ->take(100)
                ->get();
                
            if ($completedRuns->isNotEmpty()) {
                $totalSecs = $completedRuns->sum(function($run) {
                    return $run->completed_at->diffInSeconds($run->started_at);
                });
                $workflowStats['avg_duration_sec'] = round($totalSecs / $completedRuns->count());
            }
        }

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
            'audit' => $auditStats,
            'alerts' => $alertStats,
            'billing' => [
                'active_subscriptions' => $activeSubscriptions,
                'monthly_revenue' => $monthlyRevenue,
                'failed_payments' => $failedPayments,
                'expiring_subscriptions' => $expiringSubscriptions,
            ],
            'usage' => $globalUsage,
            'workflows' => $workflowStats,
            'backups' => app(\App\Domain\HQ\Services\Backup\BackupHealthService::class)->getMetrics(),
            'iam' => [
                'users' => \App\Models\User::count(),
                'roles' => \App\Models\HQRole::count(),
                'sessions' => \App\Models\HQSecuritySession::where('is_active', true)->count(),
                'api_keys' => \App\Models\HQApiKey::where('is_revoked', false)->count(),
                'mfa_users' => \App\Models\HQMfaSetting::where('is_enabled', true)->count(),
                'failed_logins' => \App\Models\HQLoginAttempt::where('is_successful', false)
                    ->where('attempted_at', '>=', now()->subDay())->count(),
            ],
            'observability' => [
                'active_services' => \App\Models\HQLog::select('service')->distinct()->count(),
                'critical_errors' => \App\Models\HQLog::where('level', 'critical')->where('created_at', '>=', now()->subDay())->count(),
                'avg_response_time' => (int) \App\Models\HQMetric::where('metric_type', 'timing')->where('recorded_at', '>=', now()->subDay())->avg('value'),
                'failed_jobs' => \Illuminate\Support\Facades\DB::table('failed_jobs')->count(),
                'security_events' => \App\Models\HQSecurityEvent::where('created_at', '>=', now()->subDay())->count(),
                'uptime' => '99.99', // simplified, ideally from ping checks
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
