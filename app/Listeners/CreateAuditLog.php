<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Domain\HQ\Services\HQAuditService;
use App\Events\LicenseChanged;
use App\Events\RemoteCommandExecuted;
use App\Events\UpdateCompleted;
use App\Events\ConfigurationChanged;
use App\Events\BackupCompleted;

class CreateAuditLog
{
    public function handle(object $event): void
    {
        if ($event instanceof LicenseChanged) {
            HQAuditService::logSystemAction(
                action: $event->action,
                category: 'license',
                severity: str_contains($event->action, 'suspended') ? 'warning' : 'info',
                tenantId: $event->license->tenant_id,
                metadata: [
                    'license_id' => $event->license->id,
                    'old_values' => $event->oldValues,
                    'new_values' => $event->newValues,
                ]
            );
        }
        
        elseif ($event instanceof RemoteCommandExecuted) {
            HQAuditService::logSystemAction(
                action: $event->action,
                category: 'command',
                severity: str_contains($event->action, 'failed') ? 'danger' : 'info',
                description: $event->description,
                systemInstanceId: $event->command->system_instance_id,
                metadata: [
                    'command_id' => $event->command->id,
                    'command_type' => $event->command->command_type,
                ]
            );
        }
        
        elseif ($event instanceof UpdateCompleted) {
            HQAuditService::logSystemAction(
                action: $event->action,
                category: 'update',
                severity: 'info',
                description: $event->description,
                systemInstanceId: $event->job->system_instance_id,
                tenantId: $event->job->tenant_id,
                metadata: [
                    'job_id' => $event->job->id,
                    'version_id' => $event->job->hq_version_id,
                ]
            );
        }
        
        elseif ($event instanceof ConfigurationChanged) {
            HQAuditService::logSystemAction(
                action: $event->action,
                category: 'configuration',
                severity: str_contains($event->action, 'rollback') ? 'warning' : 'info',
                description: $event->description,
                systemInstanceId: $event->version->profile->system_instance_id ?? null,
                tenantId: $event->version->profile->tenant_id ?? null,
                metadata: [
                    'version_id' => $event->version->id,
                    'profile_id' => $event->version->hq_configuration_profile_id,
                ]
            );
        }
        
        elseif ($event instanceof BackupCompleted) {
            $severity = 'info';
            if (str_contains($event->action, 'failed')) {
                $severity = 'danger';
            } elseif (str_contains($event->action, 'restored')) {
                $severity = 'warning';
            }
            
            HQAuditService::logSystemAction(
                action: $event->action,
                category: 'backup',
                severity: $severity,
                description: $event->description,
                systemInstanceId: $event->job->system_instance_id,
                metadata: [
                    'job_id' => $event->job->id,
                    'policy_id' => $event->job->backup_policy_id,
                ]
            );
        }
        
        elseif ($event instanceof \Illuminate\Auth\Events\Failed) {
            HQAuditService::logSecurityEvent(
                action: 'authentication.failed',
                severity: 'warning',
                description: 'Failed login attempt for user: ' . ($event->credentials['email'] ?? 'unknown'),
                metadata: [
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]
            );
        }
    }
}
