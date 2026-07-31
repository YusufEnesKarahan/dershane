<?php

namespace App\Domain\HQ\Services\Configuration;

use App\Models\HQConfiguration;
use App\Models\HQConfigurationVersion;
use App\Models\HQConfigurationRollback;

class ConfigurationVersionService
{
    /**
     * Create a new version snapshot for a configuration.
     */
    public function createVersion(HQConfiguration $config, string $versionTag): HQConfigurationVersion
    {
        return HQConfigurationVersion::create([
            'configuration_id' => $config->id,
            'version_tag' => $versionTag,
            'value' => $config->value,
            'created_by' => auth()->id() ?? 'system',
        ]);
    }

    /**
     * Compare a configuration against a specific version.
     */
    public function diff(HQConfiguration $config, HQConfigurationVersion $version): array
    {
        return [
            'current' => $config->value,
            'version' => $version->value,
            'is_different' => $config->value !== $version->value,
        ];
    }

    /**
     * Rollback a configuration to a specific version.
     */
    public function rollback(HQConfiguration $config, HQConfigurationVersion $version)
    {
        $oldValue = $config->value;
        
        // Update configuration value
        $config->update(['value' => $version->value]);

        // Record the rollback
        $rollback = HQConfigurationRollback::create([
            'configuration_id' => $config->id,
            'version_id' => $version->id,
            'from_value' => $oldValue,
            'to_value' => $version->value,
            'executed_by' => auth()->id() ?? 'system',
        ]);

        // Invalidate Cache
        app(ConfigurationService::class)->invalidate($config->key, $config->tenant_id);

        event(new \App\Events\ConfigurationRollbackCompleted($rollback));

        return $rollback;
    }
}
