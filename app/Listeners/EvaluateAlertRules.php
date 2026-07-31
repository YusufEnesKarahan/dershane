<?php

namespace App\Listeners;

use App\Domain\HQ\Services\HQAlertRuleEvaluator;
use App\Events\LicenseChanged;
use App\Events\RemoteCommandExecuted;
use App\Events\UpdateCompleted;
use App\Events\BackupCompleted;
use App\Events\ConfigurationChanged;
use App\Events\SystemOfflineDetected;
use App\Events\SecurityThreatDetected;
use App\Events\BackupFailedDetected;
use Illuminate\Contracts\Queue\ShouldQueue;

class EvaluateAlertRules implements ShouldQueue
{
    protected HQAlertRuleEvaluator $evaluator;

    public function __construct(HQAlertRuleEvaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    public function handle($event)
    {
        $context = [];
        $eventType = 'unknown';

        if ($event instanceof LicenseChanged) {
            $eventType = $event->action; // e.g. 'license.expired'
            $context = [
                'type' => 'license',
                'tenant_id' => $event->license->tenant_id,
                'system_instance_id' => $event->license->system_instance_id,
                'message' => "License action: {$event->action} for license ID: {$event->license->id}",
                'metadata' => $event->metadata,
            ];
        } elseif ($event instanceof RemoteCommandExecuted) {
            $eventType = 'command.executed';
            $context = [
                'type' => 'command',
                'system_instance_id' => $event->command->system_instance_id,
                'message' => "Command {$event->command->command} executed. Status: {$event->command->status}",
                'metadata' => ['command_id' => $event->command->id, 'status' => $event->command->status],
            ];
        } elseif ($event instanceof UpdateCompleted) {
            $eventType = $event->job->status === 'failed' ? 'update.failed' : 'update.completed';
            $context = [
                'type' => 'update',
                'tenant_id' => $event->job->tenant_id,
                'system_instance_id' => $event->job->system_instance_id,
                'message' => "Update {$event->job->version} finished with status: {$event->job->status}",
                'metadata' => ['job_id' => $event->job->id],
            ];
        } elseif ($event instanceof BackupCompleted) {
            $eventType = $event->job->status === 'failed' ? 'backup.failed' : 'backup.completed';
            $context = [
                'type' => 'backup',
                'tenant_id' => $event->job->tenant_id,
                'system_instance_id' => $event->job->system_instance_id,
                'message' => "Backup {$event->job->id} finished with status: {$event->job->status}",
                'metadata' => ['job_id' => $event->job->id],
            ];
        } elseif ($event instanceof ConfigurationChanged) {
            $eventType = 'configuration.changed';
            $tenantId = isset($event->configuration) ? $event->configuration->tenant_id : ($event->profile->tenant_id ?? null);
            $profileName = isset($event->configuration) ? 'Global/Tenant Configuration' : ($event->profile->name ?? 'Unknown Profile');
            $profileId = isset($event->configuration) ? null : ($event->profile->id ?? null);

            $context = [
                'type' => 'configuration',
                'tenant_id' => $tenantId,
                'message' => "Configuration profile {$profileName} changed.",
                'metadata' => [
                    'profile_id' => $profileId,
                    'configuration_id' => isset($event->configuration) ? $event->configuration->id : null,
                ],
            ];
        } elseif ($event instanceof SystemOfflineDetected) {
            $eventType = 'system.offline';
            $context = [
                'type' => 'system_offline',
                'tenant_id' => $event->systemInstance->tenant_id,
                'system_instance_id' => $event->systemInstance->id,
                'message' => "System instance {$event->systemInstance->system_name} has been offline for {$event->minutesOffline} minutes.",
                'metadata' => ['minutes_offline' => $event->minutesOffline],
            ];
        } elseif ($event instanceof SecurityThreatDetected) {
            $eventType = 'security.threat';
            $context = [
                'type' => 'security',
                'tenant_id' => $event->systemInstance ? $event->systemInstance->tenant_id : null,
                'system_instance_id' => $event->systemInstance ? $event->systemInstance->id : null,
                'message' => $event->threatDetails['message'] ?? 'Security threat detected.',
                'metadata' => $event->threatDetails,
            ];
        } elseif ($event instanceof BackupFailedDetected) {
            $eventType = 'backup.failed';
            $context = [
                'type' => 'backup',
                'tenant_id' => $event->systemInstance ? $event->systemInstance->tenant_id : null,
                'system_instance_id' => $event->systemInstance ? $event->systemInstance->id : null,
                'message' => $event->backupDetails['message'] ?? 'Backup failed.',
                'metadata' => $event->backupDetails,
            ];
        }

        $this->evaluator->evaluateEvent($eventType, $context);
    }
}
