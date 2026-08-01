<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Core\Services\AuditService;
use App\Events\LicenseChanged;
use App\Events\UpdateCompleted;
use App\Events\ConfigurationChanged;
use App\Events\BackupCompleted;

class CreateAuditLog
{
    public function handle(object $event): void
    {
        if ($event instanceof LicenseChanged) {
            AuditService::logSystemAction(
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
        
        elseif ($event instanceof UpdateCompleted) {
            AuditService::logSystemAction(
                action: $event->action,
                category: 'update',
                severity: 'info',
                description: $event->description,
                tenantId: $event->job->tenant_id,
                metadata: [
                    'job_id' => $event->job->id,
                ]
            );
        }
        
        elseif ($event instanceof ConfigurationChanged) {
            $versionId = is_object($event->version) ? $event->version->id : null;
            $tenantId = isset($event->configuration) ? $event->configuration->tenant_id : (is_object($event->version) ? ($event->version->profile->tenant_id ?? null) : null);

            AuditService::logSystemAction(
                action: $event->action ?? 'updated',
                category: 'configuration',
                severity: str_contains($event->action ?? '', 'rollback') ? 'warning' : 'info',
                description: $event->description ?? '',
                tenantId: $tenantId,
                metadata: [
                    'version_id' => $versionId,
                    'configuration_id' => isset($event->configuration) ? $event->configuration->id : null,
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
            
            AuditService::logSystemAction(
                action: $event->action,
                category: 'backup',
                severity: $severity,
                description: $event->description,
                metadata: [
                    'job_id' => $event->job->id,
                    'policy_id' => $event->job->backup_policy_id,
                ]
            );
        }
        
        elseif ($event instanceof \Illuminate\Auth\Events\Failed) {
            AuditService::logSecurityEvent(
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
