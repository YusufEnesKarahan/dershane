<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQUpdateJob;
use App\Models\HQVersion;
use App\Models\HQSystemInstance;
use App\Models\HQTenant;
use App\Domain\HQ\Services\HQUpdateService;
use Illuminate\Http\Request;

class HQUpdateController extends Controller
{
    public function __construct(
        protected HQUpdateService $updateService
    ) {}

    public function index()
    {
        $jobs = HQUpdateJob::with(['version', 'systemInstance', 'tenant'])->latest()->paginate(20);
        return view('admin.hq.updates.index', compact('jobs'));
    }

    public function show(HQUpdateJob $update)
    {
        $update->load(['version', 'systemInstance', 'tenant']);
        return view('admin.hq.updates.show', compact('update'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'version_id' => 'required|exists:hq_versions,id',
            'target_type' => 'required|in:single,tenant,global',
            'system_instance_id' => 'required_if:target_type,single|exists:hq_system_instances,id',
            'tenant_id' => 'required_if:target_type,tenant|exists:hq_tenants,id',
        ]);

        $version = HQVersion::findOrFail($validated['version_id']);

        if ($validated['target_type'] === 'single') {
            $instance = HQSystemInstance::findOrFail($validated['system_instance_id']);
            $this->updateService->dispatchUpdateToInstance($instance, $version);
        } elseif ($validated['target_type'] === 'tenant') {
            $tenant = HQTenant::findOrFail($validated['tenant_id']);
            $this->updateService->dispatchUpdateToTenant($tenant, $version);
        } elseif ($validated['target_type'] === 'global') {
            $this->updateService->dispatchUpdateGlobal($version);
        }

        return redirect()->route('admin.platform.hq_central.updates.index')->with('success', 'Update dispatched successfully.');
    }

    public function cancel(HQUpdateJob $update)
    {
        $this->updateService->cancelUpdate($update);
        return back()->with('success', 'Update cancelled successfully.');
    }

    public function retry(HQUpdateJob $update)
    {
        $this->updateService->retryUpdate($update);
        return back()->with('success', 'Update retried successfully.');
    }
}
