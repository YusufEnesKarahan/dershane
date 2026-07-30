<?php

namespace App\Domain\HQ\Services;

use App\Models\HQSystemInstance;
use App\Models\HQLicense;
use App\Models\HQCentralBackupJob;
use App\Events\SystemOfflineDetected;
use App\Events\LicenseChanged;
use App\Events\BackupFailedDetected;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HQSchedulerService
{
    /**
     * Run all hourly scheduled checks.
     */
    public function runHourlyChecks()
    {
        Log::info('HQSchedulerService: Running hourly checks.');
        
        $this->detectOfflineSystems();
        $this->checkExpiringLicenses();
        $this->checkFailedBackups();
        $this->checkWorkflowTimeouts();
        $this->runFleetChecks();
        $this->runBackupChecks('hourly');
        
        // Aggregate usage data hourly
        app(\App\Domain\HQ\Services\UsageAggregationService::class)->aggregate('hourly');
    }

    /**
     * Detect systems that haven't sent a heartbeat/telemetry recently.
     */
    protected function detectOfflineSystems()
    {
        // Consider offline if not seen in 15 minutes
        $threshold = now()->subMinutes(15);
        
        $offlineInstances = HQSystemInstance::where('status', 'online')
            ->where('last_seen_at', '<', $threshold)
            ->get();

        foreach ($offlineInstances as $instance) {
            $instance->update(['status' => 'offline']);
            
            $minutesOffline = now()->diffInMinutes($instance->last_seen_at);
            event(new SystemOfflineDetected($instance, $minutesOffline));
        }
    }

    /**
     * Check licenses that are expiring soon or already expired.
     */
    protected function checkExpiringLicenses()
    {
        $thirtyDaysFromNow = now()->addDays(30);

        // Notify for licenses expiring in < 30 days
        $expiringLicenses = HQLicense::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $thirtyDaysFromNow)
            ->where('expires_at', '>', now())
            ->get();

        foreach ($expiringLicenses as $license) {
            // We can dispatch LicenseChanged or a specific event
            event(new LicenseChanged('license.expiring', $license, [], []));
        }

        // Handle completely expired licenses
        $expiredLicenses = HQLicense::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();
            
        foreach ($expiredLicenses as $license) {
            $license->update(['status' => 'expired']);
            event(new LicenseChanged('license.expired', $license, ['status' => 'active'], ['status' => 'expired']));
        }
    }

    /**
     * Check for backups that have failed or been stuck for a long time.
     */
    protected function checkFailedBackups()
    {
        // Example: Stuck backups that are pending for more than 2 hours
        $threshold = now()->subHours(2);
        
        // This assumes HQCentralBackupJob model exists from previous sprints
        if (class_exists(HQCentralBackupJob::class)) {
            $stuckBackups = HQCentralBackupJob::where('status', 'pending')
                ->where('created_at', '<', $threshold)
                ->get();
                
            foreach ($stuckBackups as $backup) {
                $backup->update(['status' => 'failed']);
                
                $instance = HQSystemInstance::find($backup->system_instance_id);
                event(new BackupFailedDetected($instance, [
                    'job_id' => $backup->id,
                    'message' => 'Backup timed out and was marked as failed by scheduler.'
                ]));
            }
        }

    /**
     * Check for workflows that are stuck running for too long (timeout).
     */
    protected function checkWorkflowTimeouts()
    {
        // Example: Stuck workflows for more than 4 hours
        $threshold = now()->subHours(4);
        
        if (class_exists(\App\Models\HQWorkflowRun::class)) {
            $stuckRuns = \App\Models\HQWorkflowRun::where('status', 'running')
                ->where('started_at', '<', $threshold)
                ->get();
                
            foreach ($stuckRuns as $run) {
                app(\App\Domain\HQ\Services\Workflow\WorkflowExecutionService::class)->finishRun(
                    $run,
                    'timeout',
                    'Workflow exceeded execution time limit.'
                );
            }
        }
    }

    /**
     * Run Fleet Orchestration periodic checks.
     * Handles deployment timeouts, maintenance window automation, etc.
     */
    protected function runFleetChecks()
    {
        Log::info('HQSchedulerService: Running fleet orchestration checks.');

        // 1. Process Maintenance Windows
        app(\App\Domain\HQ\Services\Fleet\MaintenanceService::class)->processScheduledWindows();

        // 2. Deployment Timeouts
        $threshold = now()->subHours(2);
        if (class_exists(\App\Models\HQDeployment::class)) {
            $stuckDeployments = \App\Models\HQDeployment::where('status', 'running')
                ->where('started_at', '<', $threshold)
                ->get();
                
            foreach ($stuckDeployments as $deployment) {
                // If stuck, fail it and trigger rollback if canary/rolling
                app(\App\Domain\HQ\Services\Fleet\DeploymentService::class)->completeTarget(
                    $deployment->targets()->where('status', 'running')->first(),
                    false,
                    'Deployment timed out.'
                );
            }
        }
    }

    /**
     * Run backup and disaster recovery orchestrations.
     */
    protected function runBackupChecks(string $frequency = 'hourly')
    {
        Log::info("HQSchedulerService: Running {$frequency} backup checks.");
        app(\App\Domain\HQ\Services\Backup\BackupPolicyService::class)->runScheduledPolicies($frequency);

        if ($frequency === 'daily') {
            \App\Jobs\PruneBackupsJob::dispatch();
        }
    }
    /**
     * Run all daily scheduled checks (like billing).
     */
    public function runDailyBillingChecks()
    {
        Log::info('HQSchedulerService: Running daily billing checks.');
        
        $this->processExpiringSubscriptions();
        
        $this->runBackupChecks('daily');
        
        // Aggregate usage data daily
        app(\App\Domain\HQ\Services\UsageAggregationService::class)->aggregate('daily');
    }

    /**
     * Run weekly aggregation.
     */
    public function runWeeklyUsageAggregation()
    {
        app(\App\Domain\HQ\Services\UsageAggregationService::class)->aggregate('weekly');
    }

    /**
     * Run monthly aggregation.
     */
    public function runMonthlyUsageAggregation()
    {
        app(\App\Domain\HQ\Services\UsageAggregationService::class)->aggregate('monthly');
    }

    /**
     * Process expiring or past due subscriptions.
     */
    protected function processExpiringSubscriptions()
    {
        $billingService = app(\App\Domain\HQ\Services\HQBillingService::class);
        $subscriptionService = app(\App\Domain\HQ\Services\HQSubscriptionService::class);

        // Find active subscriptions that need renewal today
        $dueSubscriptions = \App\Models\HQSubscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->get();

        foreach ($dueSubscriptions as $sub) {
            // Generate invoice
            $invoice = $billingService->createInvoice($sub);
            
            // Note: In a real system, you would charge the customer's card here.
            // If failed, mark invoice as failed and subscription as past_due.
            // If success, mark invoice as paid, which will renew the subscription.
            
            // For now, just generate the invoice. We'll leave it pending.
            // To simulate past due for unpaid ones:
            $sub->update(['status' => 'past_due']);
        }
        
        // Find past due subscriptions that should be cancelled after grace period (e.g. 7 days)
        $pastDueToCancel = \App\Models\HQSubscription::where('status', 'past_due')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now()->subDays(7))
            ->get();
            
        foreach ($pastDueToCancel as $sub) {
            $subscriptionService->expireSubscription($sub);
        }
    }
}
