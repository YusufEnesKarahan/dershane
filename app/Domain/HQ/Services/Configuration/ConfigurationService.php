<?php

namespace App\Domain\HQ\Services\Configuration;

use App\Models\HQConfiguration;
use App\Models\HQConfigurationChange;
use Illuminate\Support\Facades\Cache;

class ConfigurationService
{
    /**
     * Get a configuration value dynamically.
     * Uses Laravel Cache to prevent DB hits on every request.
     */
    public function get(string $key, ?int $tenantId = null, $default = null)
    {
        $cacheKey = $this->buildCacheKey($key, $tenantId);

        return Cache::rememberForever($cacheKey, function () use ($key, $tenantId, $default) {
            $query = HQConfiguration::where('key', $key);
            
            if ($tenantId) {
                $query->where(function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)
                      ->orWhereNull('tenant_id');
                })->orderByDesc('tenant_id'); // tenant_id override wins over null
            } else {
                $query->whereNull('tenant_id');
            }

            $config = $query->first();

            return $config ? $config->value : $default;
        });
    }

    /**
     * Set a configuration value and invalidate the cache.
     */
    public function set(string $key, $value, ?int $groupId = null, ?int $tenantId = null, string $type = 'string')
    {
        $existing = HQConfiguration::where('key', $key)->where('tenant_id', $tenantId)->first();
        $oldValue = $existing ? $existing->value : null;

        if ($existing && $existing->value === $value) {
            return $existing; // No change
        }

        $config = HQConfiguration::updateOrCreate(
            ['key' => $key, 'tenant_id' => $tenantId],
            ['group_id' => $groupId ?? 1, 'value' => $value, 'type' => $type]
        );

        // Record the change
        HQConfigurationChange::create([
            'configuration_id' => $config->id,
            'old_value' => $oldValue,
            'new_value' => $value,
            'changed_by' => auth()->id() ?? 'system',
        ]);

        $this->invalidate($key, $tenantId);
        
        event(new \App\Events\ConfigurationChanged($config));

        return $config;
    }

    public function delete(string $key, ?int $tenantId = null)
    {
        $config = HQConfiguration::where('key', $key)->where('tenant_id', $tenantId)->first();
        if ($config) {
            $config->delete();
            $this->invalidate($key, $tenantId);
            return true;
        }
        return false;
    }

    public function bulkUpdate(array $configurations, ?int $groupId = null, ?int $tenantId = null)
    {
        foreach ($configurations as $key => $value) {
            $type = is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : (is_array($value) ? 'json' : 'string'));
            $this->set($key, $value, $groupId, $tenantId, $type);
        }
    }

    public function invalidate(string $key, ?int $tenantId = null)
    {
        Cache::forget($this->buildCacheKey($key, $tenantId));
        // If a global config changes, it's safer to clear tenant overrides from cache as well,
        // but for simplicity in this sprint we just clear the specific key.
    }

    protected function buildCacheKey(string $key, ?int $tenantId): string
    {
        return "hq_config_{$key}_tenant_" . ($tenantId ?? 'global');
    }
}
