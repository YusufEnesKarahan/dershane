<?php

namespace App\Http\Controllers\Api\HQ;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\HQ\Services\Extension\MarketplaceService;
use App\Domain\HQ\Services\Extension\ExtensionInstallationService;
use App\Models\HQTenant;
use App\Jobs\InstallExtensionJob;
use App\Jobs\UpdateExtensionJob;
use App\Jobs\RemoveExtensionJob;

class HQMarketplaceApiController extends Controller
{
    protected $marketplaceService;
    protected $installationService;

    public function __construct(MarketplaceService $marketplaceService, ExtensionInstallationService $installationService)
    {
        $this->marketplaceService = $marketplaceService;
        $this->installationService = $installationService;
    }

    public function extensions(Request $request)
    {
        // Get tenant if passed, else null
        $tenant = $request->tenant_id ? HQTenant::find($request->tenant_id) : null;
        
        $extensions = $this->marketplaceService->getAvailableExtensions($tenant, [
            'type' => $request->query('type'),
            'search' => $request->query('search')
        ]);

        return response()->json([
            'success' => true,
            'data' => $extensions
        ]);
    }

    public function install(Request $request)
    {
        $request->validate([
            'extension_slug' => 'required|string|exists:hq_extensions,slug',
            'tenant_id' => 'required|exists:hq_tenants,id'
        ]);

        $extension = \App\Models\HQExtension::where('slug', $request->extension_slug)->firstOrFail();
        $version = $extension->versions()->orderBy('created_at', 'desc')->firstOrFail();
        $tenant = HQTenant::findOrFail($request->tenant_id);

        InstallExtensionJob::dispatch($extension, $version, $tenant);

        return response()->json([
            'success' => true,
            'message' => 'Extension installation dispatched successfully.'
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'installation_id' => 'required|exists:hq_extension_installations,id',
            'version_id' => 'required|exists:hq_extension_versions,id'
        ]);

        $installation = \App\Models\HQExtensionInstallation::findOrFail($request->installation_id);
        $newVersion = \App\Models\HQExtensionVersion::findOrFail($request->version_id);

        UpdateExtensionJob::dispatch($installation, $newVersion);

        return response()->json([
            'success' => true,
            'message' => 'Extension update dispatched successfully.'
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'installation_id' => 'required|exists:hq_extension_installations,id',
        ]);

        $installation = \App\Models\HQExtensionInstallation::findOrFail($request->installation_id);

        RemoveExtensionJob::dispatch($installation);

        return response()->json([
            'success' => true,
            'message' => 'Extension removal dispatched successfully.'
        ]);
    }
}
