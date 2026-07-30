<?php

namespace App\Domain\HQ\Services\Workflow;

use App\Models\HQTenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorkflowActionService
{
    protected WorkflowVariableResolver $resolver;

    public function __construct(WorkflowVariableResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Execute a specific action.
     * @return array|null Additional variables to merge into payload
     */
    public function execute(array $config, array $payload, ?HQTenant $tenant = null): ?array
    {
        $action = $config['action'] ?? null;

        if (!$action) {
            throw new \Exception("Action type not specified.");
        }

        return match ($action) {
            'send_notification' => $this->sendNotification($config, $payload, $tenant),
            'send_mail' => $this->sendMail($config, $payload, $tenant),
            'create_alert' => $this->createAlert($config, $payload, $tenant),
            'create_audit' => $this->createAudit($config, $payload, $tenant),
            'execute_remote_command' => $this->executeRemoteCommand($config, $payload, $tenant),
            'sync_configuration' => $this->syncConfiguration($config, $payload, $tenant),
            'retry_backup' => $this->retryBackup($config, $payload, $tenant),
            'retry_update' => $this->retryUpdate($config, $payload, $tenant),
            'clear_cache' => $this->clearCache($config, $payload, $tenant),
            'disable_license' => $this->disableLicense($config, $payload, $tenant),
            'enable_license' => $this->enableLicense($config, $payload, $tenant),
            'webhook_call' => $this->webhookCall($config, $payload),
            'stop_workflow' => $this->stopWorkflow($config, $payload),
            default => throw new \Exception("Unknown action type: {$action}"),
        };
    }

    protected function sendNotification(array $config, array $payload, ?HQTenant $tenant)
    {
        $message = $this->resolver->resolveValue($config['message'] ?? '', $payload);
        // Integrate with Notification system if exists. Let's assume we log for now.
        Log::info("Workflow Notification: {$message}");
        return ['notification_sent' => true];
    }

    protected function sendMail(array $config, array $payload, ?HQTenant $tenant)
    {
        $to = $this->resolver->resolveValue($config['to'] ?? '', $payload);
        $subject = $this->resolver->resolveValue($config['subject'] ?? '', $payload);
        $body = $this->resolver->resolveValue($config['body'] ?? '', $payload);
        // Integrate with Mail system
        Log::info("Workflow Email to {$to}: {$subject}");
        return ['mail_sent' => true];
    }

    protected function createAlert(array $config, array $payload, ?HQTenant $tenant)
    {
        $type = $this->resolver->resolveValue($config['type'] ?? 'workflow.alert', $payload);
        $severity = $this->resolver->resolveValue($config['severity'] ?? 'warning', $payload);
        $message = $this->resolver->resolveValue($config['message'] ?? '', $payload);
        
        if ($tenant) {
            $alertService = app(\App\Domain\HQ\Services\HQAlertService::class);
            $alertService->createAlert($tenant, $type, $severity, $message, $payload);
        }
        return ['alert_created' => true];
    }

    protected function createAudit(array $config, array $payload, ?HQTenant $tenant)
    {
        $action = $this->resolver->resolveValue($config['audit_action'] ?? 'workflow.audit', $payload);
        $level = $this->resolver->resolveValue($config['level'] ?? 'info', $payload);
        $description = $this->resolver->resolveValue($config['description'] ?? '', $payload);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            $action,
            $tenant,
            $payload,
            $level,
            $description
        );
        return ['audit_created' => true];
    }

    protected function executeRemoteCommand(array $config, array $payload, ?HQTenant $tenant)
    {
        if (!$tenant) {
            throw new \Exception("Cannot execute remote command without a tenant context.");
        }

        $command = $this->resolver->resolveValue($config['command'] ?? '', $payload);
        $args = $config['args'] ?? [];
        foreach ($args as $key => $val) {
            $args[$key] = $this->resolver->resolveValue($val, $payload);
        }

        $commandService = app(\App\Domain\HQ\Services\HQRemoteCommandService::class);
        $cmd = $commandService->issueCommand($tenant, $command, $args, 'admin'); // Assuming executed by admin system
        return ['command_id' => $cmd->id];
    }

    protected function syncConfiguration(array $config, array $payload, ?HQTenant $tenant)
    {
        if (!$tenant) {
            throw new \Exception("No tenant context for sync_configuration.");
        }
        
        $syncService = app(\App\Domain\HQ\Services\HQSyncService::class);
        $syncService->syncConfiguration($tenant);
        return ['config_synced' => true];
    }

    protected function retryBackup(array $config, array $payload, ?HQTenant $tenant)
    {
        // Placeholder for backup retry logic
        Log::info("Retrying backup for tenant " . ($tenant->id ?? 'global'));
        return ['backup_retried' => true];
    }

    protected function retryUpdate(array $config, array $payload, ?HQTenant $tenant)
    {
        Log::info("Retrying update for tenant " . ($tenant->id ?? 'global'));
        return ['update_retried' => true];
    }

    protected function clearCache(array $config, array $payload, ?HQTenant $tenant)
    {
        if ($tenant) {
            $commandService = app(\App\Domain\HQ\Services\HQRemoteCommandService::class);
            $commandService->issueCommand($tenant, 'cache:clear', [], 'system');
        }
        return ['cache_cleared' => true];
    }

    protected function disableLicense(array $config, array $payload, ?HQTenant $tenant)
    {
        if (!$tenant) {
            throw new \Exception("No tenant context for disable_license.");
        }
        
        $licenseService = app(\App\Domain\HQ\Services\HQLicenseService::class);
        $license = $licenseService->getActiveLicense($tenant);
        
        if ($license) {
            $licenseService->suspendLicense($license, "Suspended via Workflow");
        }
        
        return ['license_disabled' => true];
    }

    protected function enableLicense(array $config, array $payload, ?HQTenant $tenant)
    {
        if (!$tenant) {
            throw new \Exception("No tenant context for enable_license.");
        }
        
        $licenseService = app(\App\Domain\HQ\Services\HQLicenseService::class);
        $license = $licenseService->getActiveLicense($tenant);
        
        if ($license && $license->status !== 'active') {
            $license->update(['status' => 'active']);
        }
        
        return ['license_enabled' => true];
    }

    protected function webhookCall(array $config, array $payload)
    {
        $url = $this->resolver->resolveValue($config['url'] ?? '', $payload);
        $method = strtoupper($this->resolver->resolveValue($config['method'] ?? 'POST', $payload));
        $headers = $config['headers'] ?? [];
        $data = $config['data'] ?? [];
        
        foreach ($data as $key => $val) {
            $data[$key] = $this->resolver->resolveValue($val, $payload);
        }

        $response = Http::withHeaders($headers)
            ->timeout(10)
            ->send($method, $url, ['json' => $data]);
            
        return [
            'webhook_status' => $response->status(),
            'webhook_response' => $response->json(),
        ];
    }

    protected function stopWorkflow(array $config, array $payload)
    {
        throw new \Exception("Workflow stopped by user logic: " . ($config['reason'] ?? ''));
    }
}
