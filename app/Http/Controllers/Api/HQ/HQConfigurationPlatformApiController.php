<?php

namespace App\Http\Controllers\Api\HQ;

use App\Http\Controllers\Controller;
use App\Models\HQConfiguration;
use App\Models\HQConfigurationVersion;
use App\Models\HQFeatureFlag;
use App\Models\HQSecretVault;
use App\Models\HQEnvironmentProfile;
use App\Domain\HQ\Services\Configuration\ConfigurationVersionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HQConfigurationPlatformApiController extends Controller
{
    public function getConfigurations(Request $request): JsonResponse
    {
        $configs = HQConfiguration::with('group', 'versions')->get();
        return response()->json(['status' => 'success', 'data' => $configs]);
    }

    public function getVersions(Request $request): JsonResponse
    {
        $versions = HQConfigurationVersion::with('configuration')->get();
        return response()->json(['status' => 'success', 'data' => $versions]);
    }

    public function getFeatureFlags(Request $request): JsonResponse
    {
        $flags = HQFeatureFlag::with('targets')->get();
        return response()->json(['status' => 'success', 'data' => $flags]);
    }

    public function getSecrets(Request $request): JsonResponse
    {
        // For security, do not return actual decrypted secrets via bulk list API.
        // Return metadata only.
        $secrets = HQSecretVault::select('id', 'uuid', 'name', 'key', 'description', 'expires_at', 'rotation_interval', 'is_active', 'created_at')->get();
        return response()->json(['status' => 'success', 'data' => $secrets]);
    }

    public function getEnvironmentProfiles(Request $request): JsonResponse
    {
        $profiles = HQEnvironmentProfile::get();
        return response()->json(['status' => 'success', 'data' => $profiles]);
    }

    public function rollbackConfiguration(Request $request): JsonResponse
    {
        $request->validate([
            'configuration_id' => 'required|exists:hq_configurations,id',
            'version_id' => 'required|exists:hq_configuration_versions,id',
        ]);

        $config = HQConfiguration::findOrFail($request->configuration_id);
        $version = HQConfigurationVersion::findOrFail($request->version_id);

        $service = app(ConfigurationVersionService::class);
        $rollback = $service->rollback($config, $version);

        return response()->json(['status' => 'success', 'message' => 'Rolled back successfully.', 'data' => $rollback]);
    }
}
