<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQConfigurationProfile;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Domain\HQ\Services\HQConfigurationService;

class HQConfigurationController extends Controller
{
    public function __construct(
        protected HQConfigurationService $configurationService
    ) {}

    public function index(Request $request)
    {
        $query = HQConfigurationProfile::with(['tenant', 'systemInstance'])->latest();

        if ($request->has('scope')) {
            $query->where('scope', $request->scope);
        }

        $profiles = $query->paginate(20);

        return view('admin.hq.configurations.index', compact('profiles'));
    }

    public function create()
    {
        $tenants = HQTenant::active()->get();
        $instances = HQSystemInstance::with('tenant')->get();

        return view('admin.hq.configurations.create', compact('tenants', 'instances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'scope' => 'required|in:global,tenant,instance',
            'tenant_id' => 'required_if:scope,tenant|exists:hq_tenants,id|nullable',
            'system_instance_id' => 'required_if:scope,instance|exists:hq_system_instances,id|nullable',
            'environment' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $profile = $this->configurationService->createProfile($validated);

        return redirect()->route('admin.platform.hq_central.configurations.show', $profile)
                         ->with('success', 'Configuration profile created.');
    }

    public function show(HQConfigurationProfile $configuration)
    {
        $configuration->load(['items' => function($q) {
            $q->orderBy('sort_order');
        }]);

        return view('admin.hq.configurations.show', compact('configuration'));
    }

    public function storeItem(Request $request, HQConfigurationProfile $configuration)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'nullable|string',
            'type' => 'required|in:string,integer,boolean,json,encrypted',
            'is_sensitive' => 'boolean',
            'sort_order' => 'integer',
        ]);

        // boolean validation fix
        $validated['is_sensitive'] = $request->has('is_sensitive');

        $this->configurationService->setItem($configuration, $validated);

        return back()->with('success', 'Configuration item saved.');
    }

    public function destroyItem(HQConfigurationProfile $configuration, $itemId)
    {
        $configuration->items()->where('id', $itemId)->delete();
        return back()->with('success', 'Item deleted.');
    }

    public function version(HQConfigurationProfile $configuration, Request $request)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $this->configurationService->versionProfile($configuration, $validated['notes'] ?? null);

        return back()->with('success', 'New configuration version created.');
    }

    public function history(HQConfigurationProfile $configuration)
    {
        $configuration->load(['versions.creator', 'logs']);
        $versions = $configuration->versions()->latest('version')->get();
        return view('admin.hq.configurations.history', compact('configuration', 'versions'));
    }

    public function rollbackForm(HQConfigurationProfile $configuration, $version)
    {
        $targetVersion = $configuration->versions()->where('version', $version)->firstOrFail();
        return view('admin.hq.configurations.rollback', compact('configuration', 'targetVersion'));
    }

    public function rollback(HQConfigurationProfile $configuration, $version)
    {
        $this->configurationService->rollbackProfile($configuration, (int)$version);

        return redirect()->route('admin.platform.hq_central.configurations.show', $configuration)
                         ->with('success', "Rolled back to version {$version}.");
    }
}
