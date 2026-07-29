<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQSystemInstance;
use App\Models\HQConfigurationProfile;
use App\Models\HQConfigurationVersion;

class HQConfigurationApiController extends Controller
{
    /**
     * Endpoint for an ERP instance to pull its configuration.
     * The instance authenticates via HMAC signature and passes its system_uuid.
     */
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'system_uuid' => 'required|string|exists:hq_system_instances,system_uuid',
            'environment' => 'nullable|string',
        ]);

        $instance = HQSystemInstance::where('system_uuid', $validated['system_uuid'])->firstOrFail();

        // Find the applicable profile. Priority: Instance -> Tenant -> Global
        
        $profile = HQConfigurationProfile::where('status', 'active')
            ->where(function ($query) use ($instance, $validated) {
                // 1. Instance specific
                $query->where('system_instance_id', $instance->id)
                      ->where('scope', 'instance');
            })->first();

        if (!$profile && $instance->tenant_id) {
            $profile = HQConfigurationProfile::where('status', 'active')
                ->where(function ($query) use ($instance) {
                    // 2. Tenant specific
                    $query->where('tenant_id', $instance->tenant_id)
                          ->where('scope', 'tenant');
                })->first();
        }

        if (!$profile) {
            // 3. Global
            $profile = HQConfigurationProfile::where('status', 'active')
                ->where('scope', 'global')
                ->first();
        }

        if (!$profile) {
            return response()->json([
                'configurations' => [],
                'version' => 0,
                'message' => 'No configuration profile found.'
            ], 200);
        }

        // Return current active items in the profile
        // Get items and construct response
        $items = $profile->items()->orderBy('sort_order')->get();
        $latestVersion = $profile->versions()->max('version') ?? 1;

        $configurations = $items->map(function ($item) {
            return [
                'key' => $item->key,
                'value' => $item->decrypted_value, // We send decrypted value over HTTPS (and HMAC verified)
                'type' => $item->type,
            ];
        });

        return response()->json([
            'configurations' => $configurations,
            'version' => $latestVersion,
            'profile_uuid' => $profile->uuid,
            'scope' => $profile->scope,
        ], 200);
    }

    /**
     * Endpoint for ERP to report sync result (success or failure).
     */
    public function report(Request $request)
    {
        $validated = $request->validate([
            'system_uuid' => 'required|string|exists:hq_system_instances,system_uuid',
            'profile_uuid' => 'required|string|exists:hq_configuration_profiles,uuid',
            'status' => 'required|in:success,failed',
            'message' => 'nullable|string',
        ]);

        $instance = HQSystemInstance::where('system_uuid', $validated['system_uuid'])->firstOrFail();
        $profile = HQConfigurationProfile::where('uuid', $validated['profile_uuid'])->firstOrFail();

        // We can log this in hq_configuration_logs
        \App\Models\HQConfigurationLog::create([
            'profile_id' => $profile->id,
            'system_instance_id' => $instance->id,
            'action' => 'remote_sync',
            'status' => $validated['status'],
            'response' => ['message' => $validated['message']],
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Endpoint for ERP to fetch a specific version (rollback scenario).
     */
    public function version(Request $request)
    {
        $validated = $request->validate([
            'system_uuid' => 'required|string|exists:hq_system_instances,system_uuid',
            'profile_uuid' => 'required|string|exists:hq_configuration_profiles,uuid',
            'version' => 'required|integer',
        ]);

        $profile = HQConfigurationProfile::where('uuid', $validated['profile_uuid'])->firstOrFail();
        $version = HQConfigurationVersion::where('profile_id', $profile->id)
            ->where('version', $validated['version'])
            ->firstOrFail();

        // The snapshot has raw values (encrypted). We need to decrypt them if they were sensitive.
        $configurations = collect($version->configuration_snapshot)->map(function ($itemData) {
            $value = $itemData['value'];
            if (($itemData['type'] === 'encrypted' || $itemData['is_sensitive']) && $value !== null) {
                try {
                    $value = \Illuminate\Support\Facades\Crypt::decryptString($value);
                } catch (\Exception $e) {
                    $value = null;
                }
            }

            return [
                'key' => $itemData['key'],
                'value' => $value,
                'type' => $itemData['type'],
            ];
        });

        return response()->json([
            'configurations' => $configurations,
            'version' => $version->version,
            'profile_uuid' => $profile->uuid,
        ], 200);
    }
}
