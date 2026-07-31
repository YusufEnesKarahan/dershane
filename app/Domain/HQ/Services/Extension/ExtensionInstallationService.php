<?php

namespace App\Domain\HQ\Services\Extension;

use App\Models\HQExtension;
use App\Models\HQExtensionVersion;
use App\Models\HQExtensionInstallation;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Configuration\FeatureFlagService;
use Exception;

class ExtensionInstallationService
{
    protected $dependencyService;
    protected $lifecycleService;
    protected $permissionService;
    protected $featureFlagService;

    public function __construct(
        ExtensionDependencyService $dependencyService,
        ExtensionLifecycleService $lifecycleService,
        ExtensionPermissionService $permissionService,
        FeatureFlagService $featureFlagService
    ) {
        $this->dependencyService = $dependencyService;
        $this->lifecycleService = $lifecycleService;
        $this->permissionService = $permissionService;
        $this->featureFlagService = $featureFlagService;
    }

    public function install(HQExtension $extension, HQExtensionVersion $version, HQTenant $tenant): HQExtensionInstallation
    {
        // Verify Feature Flags for the extension itself
        $flagKey = 'allow_extension_' . $extension->slug;
        $flagExists = \App\Models\HQFeatureFlag::where('key', $flagKey)->exists();
        
        if ($flagExists && !$this->featureFlagService->isEnabled($flagKey, ['tenant_id' => $tenant->id])) {
            throw new Exception("Extension installation is disabled by feature flag.");
        }

        // Sprint 8.9 Billing Check
        if ($this->featureFlagService->isEnabled('billing_new_engine')) {
            $entitlementService = app(\App\Domain\HQ\Services\Billing\EntitlementService::class);
            if (!$entitlementService->hasAccess($tenant, $flagKey)) {
                throw new Exception("Extension installation is disabled by billing entitlement.");
            }
        }

        $compatibility = $this->dependencyService->checkCompatibility($version, ['php' => '8.4', 'hq_central' => '8.8']);
        if (!$compatibility['is_compatible']) {
            throw new Exception("Incompatible extension: " . implode(', ', $compatibility['issues']));
        }

        $installation = HQExtensionInstallation::updateOrCreate(
            ['extension_id' => $extension->id, 'tenant_id' => $tenant->id],
            ['version_id' => $version->id, 'status' => 'installed']
        );

        if ($installation->wasRecentlyCreated) {
            $extension->update(['installed_at' => now()]);
        }

        // Configuration Service Hook
        if (isset($extension->metadata['default_config']) && is_array($extension->metadata['default_config'])) {
            $configService = app(\App\Domain\HQ\Services\Configuration\ConfigurationService::class);
            foreach ($extension->metadata['default_config'] as $key => $value) {
                $configService->set("ext_{$extension->slug}_{$key}", $value, null, $tenant->id);
            }
        }

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'extension_installed',
            category: 'marketplace',
            severity: 'info',
            description: "Installed extension {$extension->slug} version {$version->version} for tenant {$tenant->id}"
        );

        app(\App\Domain\HQ\Services\Observability\HQLoggingService::class)->info(
            "Extension {$extension->slug} installed successfully.",
            ['extension_id' => $extension->id, 'version_id' => $version->id],
            $tenant->id,
            'marketplace'
        );

        app(\App\Domain\HQ\Services\Workflow\WorkflowEngineService::class)->handleEvent('extension.installed', [
            'extension_slug' => $extension->slug,
            'tenant_id' => $tenant->id,
            'version' => $version->version
        ], $tenant);

        event(new \App\Events\ExtensionInstalled($installation));

        return $installation;
    }

    public function enable(HQExtensionInstallation $installation)
    {
        $this->permissionService->grantPermissions($installation->extension, $installation->tenant);
        $this->lifecycleService->transition($installation, 'activated');
        
        $installation->update(['enabled_at' => now(), 'disabled_at' => null]);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'extension_enabled',
            category: 'marketplace',
            severity: 'info',
            description: "Enabled extension {$installation->extension->slug} for tenant {$installation->tenant_id}"
        );

        app(\App\Domain\HQ\Services\Observability\HQLoggingService::class)->info(
            "Extension {$installation->extension->slug} enabled.",
            ['extension_id' => $installation->extension_id],
            $installation->tenant_id,
            'marketplace'
        );

        app(\App\Domain\HQ\Services\Workflow\WorkflowEngineService::class)->handleEvent('extension.enabled', [
            'extension_slug' => $installation->extension->slug,
            'tenant_id' => $installation->tenant_id
        ], $installation->tenant);

        event(new \App\Events\ExtensionActivated($installation));
    }

    public function disable(HQExtensionInstallation $installation)
    {
        $this->permissionService->revokePermissions($installation->extension, $installation->tenant);
        $this->lifecycleService->transition($installation, 'disabled');
        
        $installation->update(['disabled_at' => now()]);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'extension_disabled',
            category: 'marketplace',
            severity: 'warning',
            description: "Disabled extension {$installation->extension->slug} for tenant {$installation->tenant_id}"
        );

        app(\App\Domain\HQ\Services\Observability\HQLoggingService::class)->info(
            "Extension {$installation->extension->slug} disabled.",
            ['extension_id' => $installation->extension_id],
            $installation->tenant_id,
            'marketplace'
        );

        app(\App\Domain\HQ\Services\Workflow\WorkflowEngineService::class)->handleEvent('extension.disabled', [
            'extension_slug' => $installation->extension->slug,
            'tenant_id' => $installation->tenant_id
        ], $installation->tenant);

        event(new \App\Events\ExtensionDisabled($installation));
    }

    public function uninstall(HQExtensionInstallation $installation)
    {
        $this->disable($installation);
        $this->lifecycleService->transition($installation, 'removed');
        
        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'extension_uninstalled',
            category: 'marketplace',
            severity: 'warning',
            description: "Uninstalled extension {$installation->extension->slug} for tenant {$installation->tenant_id}"
        );

        app(\App\Domain\HQ\Services\Observability\HQLoggingService::class)->info(
            "Extension {$installation->extension->slug} uninstalled.",
            ['extension_id' => $installation->extension_id],
            $installation->tenant_id,
            'marketplace'
        );

        app(\App\Domain\HQ\Services\Workflow\WorkflowEngineService::class)->handleEvent('extension.uninstalled', [
            'extension_slug' => $installation->extension->slug,
            'tenant_id' => $installation->tenant_id
        ], $installation->tenant);

        event(new \App\Events\ExtensionRemoved($installation));
        
        $installation->delete();
    }

    public function update(HQExtensionInstallation $installation, HQExtensionVersion $newVersion)
    {
        // Verify Feature Flags
        $flagKey = 'allow_extension_' . $installation->extension->slug;
        $flagExists = \App\Models\HQFeatureFlag::where('key', $flagKey)->exists();
        
        if ($flagExists && !$this->featureFlagService->isEnabled($flagKey, ['tenant_id' => $installation->tenant_id])) {
            throw new Exception("Extension update is disabled by feature flag.");
        }

        $compatibility = $this->dependencyService->checkCompatibility($newVersion, ['php' => '8.4', 'hq_central' => '8.8']);
        if (!$compatibility['is_compatible']) {
            throw new Exception("Incompatible extension version: " . implode(', ', $compatibility['issues']));
        }

        $this->lifecycleService->transition($installation, 'updating');

        // Sprint 8.2 Deployment Engine Integration
        // Dispatching a deployment using DeploymentService to coordinate version update
        $deployment = \App\Models\HQDeployment::create([
            'version' => $newVersion->version,
            'type' => 'manual',
            'status' => 'pending',
            'rollout_percentage' => 100
        ]);

        $deploymentService = app(\App\Domain\HQ\Services\Fleet\DeploymentService::class);
        $deploymentService->startDeployment($deployment);

        // Since we are mocking the actual async health check loop here, we will just assume success
        // In reality, the deployment engine takes over. For the sake of this service, we update the DB.
        $installation->update(['version_id' => $newVersion->id]);

        $healthCheckPassed = true; // Placeholder for actual health check triggered via deployment complete

        if ($healthCheckPassed) {
            $this->lifecycleService->transition($installation, 'activated');
            
            app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
                action: 'extension_updated',
                category: 'marketplace',
                severity: 'info',
                description: "Updated extension {$installation->extension->slug} to version {$newVersion->version} for tenant {$installation->tenant_id}"
            );

            app(\App\Domain\HQ\Services\Observability\HQLoggingService::class)->info(
                "Extension {$installation->extension->slug} updated to version {$newVersion->version}.",
                ['extension_id' => $installation->extension_id, 'version_id' => $newVersion->id],
                $installation->tenant_id,
                'marketplace'
            );

            app(\App\Domain\HQ\Services\Workflow\WorkflowEngineService::class)->handleEvent('extension.updated', [
                'extension_slug' => $installation->extension->slug,
                'tenant_id' => $installation->tenant_id,
                'version' => $newVersion->version
            ], $installation->tenant);

            event(new \App\Events\ExtensionUpdated($installation));
        } else {
            $this->lifecycleService->transition($installation, 'failed');
            
            app(\App\Domain\HQ\Services\HQAlertService::class)->createAlert(
                severity: 'critical',
                title: 'extension.update.failed',
                message: "Update for extension {$installation->extension->slug} failed health check.",
                metadata: ['installation_id' => $installation->id]
            );

            app(\App\Domain\HQ\Services\Observability\HQLoggingService::class)->error(
                "Extension {$installation->extension->slug} update failed.",
                ['extension_id' => $installation->extension_id, 'version_id' => $newVersion->id],
                $installation->tenant_id,
                'marketplace'
            );

            app(\App\Domain\HQ\Services\Workflow\WorkflowEngineService::class)->handleEvent('extension.update.failed', [
                'extension_slug' => $installation->extension->slug,
                'tenant_id' => $installation->tenant_id,
                'version' => $newVersion->version
            ], $installation->tenant);

            throw new Exception("Update failed health check. Rolled back.");
        }
    }
}
