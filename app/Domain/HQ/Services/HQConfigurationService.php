<?php

namespace App\Domain\HQ\Services;

use App\Models\HQConfigurationProfile;
use App\Models\HQConfigurationItem;
use App\Models\HQConfigurationVersion;
use App\Models\HQConfigurationLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HQConfigurationService
{
    /**
     * Create a new configuration profile.
     */
    public function createProfile(array $data): HQConfigurationProfile
    {
        return DB::transaction(function () use ($data) {
            $profile = HQConfigurationProfile::create([
                'name' => $data['name'],
                'scope' => $data['scope'],
                'tenant_id' => $data['tenant_id'] ?? null,
                'system_instance_id' => $data['system_instance_id'] ?? null,
                'environment' => $data['environment'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => 'active',
            ]);

            $this->logAction($profile, 'create_profile', 'success', null, $profile->toArray());

            return $profile;
        });
    }

    /**
     * Update an existing profile.
     */
    public function updateProfile(HQConfigurationProfile $profile, array $data): HQConfigurationProfile
    {
        return DB::transaction(function () use ($profile, $data) {
            $oldValue = $profile->toArray();
            
            $profile->update([
                'name' => $data['name'] ?? $profile->name,
                'description' => $data['description'] ?? $profile->description,
                'status' => $data['status'] ?? $profile->status,
                'environment' => $data['environment'] ?? $profile->environment,
            ]);

            $this->logAction($profile, 'update_profile', 'success', $oldValue, $profile->fresh()->toArray());

            return $profile;
        });
    }

    /**
     * Add or update an item in a profile.
     */
    public function setItem(HQConfigurationProfile $profile, array $itemData): HQConfigurationItem
    {
        return DB::transaction(function () use ($profile, $itemData) {
            $item = HQConfigurationItem::firstOrNew([
                'profile_id' => $profile->id,
                'key' => $itemData['key'],
            ]);

            $oldValue = $item->exists ? $item->toArray() : null;

            $item->type = $itemData['type'] ?? $item->type ?? 'string';
            $item->is_sensitive = $itemData['is_sensitive'] ?? $item->is_sensitive ?? false;
            $item->sort_order = $itemData['sort_order'] ?? $item->sort_order ?? 0;
            // setValueAttribute will handle encryption if needed based on type/is_sensitive
            $item->value = $itemData['value'];
            $item->save();

            $this->logAction($profile, 'set_item', 'success', $oldValue, $item->toArray());

            return $item;
        });
    }

    /**
     * Generate a snapshot of the current configuration.
     */
    public function generateSnapshot(HQConfigurationProfile $profile): array
    {
        $items = $profile->items()->orderBy('sort_order')->get();
        $snapshot = [];
        
        foreach ($items as $item) {
            $snapshot[] = [
                'key' => $item->key,
                'value' => $item->value, // Encrypted values stay encrypted in snapshot
                'type' => $item->type,
                'is_sensitive' => $item->is_sensitive,
            ];
        }

        return $snapshot;
    }

    /**
     * Version a profile based on its current items.
     */
    public function versionProfile(HQConfigurationProfile $profile, string $notes = null): HQConfigurationVersion
    {
        return DB::transaction(function () use ($profile, $notes) {
            $latestVersion = $profile->versions()->max('version') ?? 0;
            $newVersionNumber = $latestVersion + 1;

            $snapshot = $this->generateSnapshot($profile);

            $version = HQConfigurationVersion::create([
                'profile_id' => $profile->id,
                'version' => $newVersionNumber,
                'created_by' => Auth::id(),
                'notes' => $notes,
                'configuration_snapshot' => $snapshot,
            ]);

            $this->logAction($profile, 'create_version', 'success', null, $version->toArray());

            return $version;
        });
    }

    /**
     * Rollback a profile to a specific version.
     */
    public function rollbackProfile(HQConfigurationProfile $profile, int $versionNumber): HQConfigurationVersion
    {
        return DB::transaction(function () use ($profile, $versionNumber) {
            $targetVersion = $profile->versions()->where('version', $versionNumber)->firstOrFail();

            // Clear current items
            $profile->items()->delete();

            // Restore from snapshot
            foreach ($targetVersion->configuration_snapshot as $itemData) {
                HQConfigurationItem::create([
                    'profile_id' => $profile->id,
                    'key' => $itemData['key'],
                    'value' => $itemData['value'],
                    'type' => $itemData['type'],
                    'is_sensitive' => $itemData['is_sensitive'],
                ]);
            }

            // Create a new version for the rollback
            $newVersion = $this->versionProfile($profile, "Rolled back to version {$versionNumber}");
            
            $this->logAction($profile, 'rollback_profile', 'success', null, ['rolled_back_to' => $versionNumber, 'new_version' => $newVersion->version]);

            return $newVersion;
        });
    }

    /**
     * Internal logger.
     */
    protected function logAction(HQConfigurationProfile $profile, string $action, string $status, ?array $oldValue = null, ?array $newValue = null)
    {
        HQConfigurationLog::create([
            'profile_id' => $profile->id,
            'action' => $action,
            'status' => $status,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ]);
    }
}
