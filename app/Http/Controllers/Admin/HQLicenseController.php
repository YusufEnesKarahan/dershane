<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQLicense;
use App\Models\HQTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Domain\HQ\Services\HQLicenseService;

class HQLicenseController extends Controller
{
    public function __construct(
        protected HQLicenseService $licenseService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('hq.manageLicense');
        
        $this->licenseService->checkExpiration();
        
        $query = HQLicense::with(['tenant', 'systemInstance'])->latest();
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $licenses = $query->paginate(20);
        $tenants = HQTenant::all();
        
        return view('admin.hq.licenses.index', compact('licenses', 'tenants'));
    }

    public function store(Request $request)
    {
        Gate::authorize('hq.manageLicense');
        
        $data = $request->validate([
            'tenant_id' => 'required|exists:hq_tenants,id',
            'plan' => 'required|string|max:255',
            'status' => 'nullable|string|in:active,suspended,expired,pending',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);
        
        $this->licenseService->createLicense($data);
        
        return back()->with('success', 'License created successfully.');
    }

    public function show($id)
    {
        Gate::authorize('hq.manageLicense');
        
        $license = HQLicense::with(['tenant', 'systemInstance', 'licenseFeatures'])->findOrFail($id);
        
        return view('admin.hq.licenses.show', compact('license'));
    }

    public function activate($id)
    {
        Gate::authorize('hq.manageLicense');
        
        $license = HQLicense::findOrFail($id);
        $this->licenseService->activateLicense($license);
        
        return back()->with('success', 'License activated.');
    }

    public function suspend($id)
    {
        Gate::authorize('hq.manageLicense');
        
        $license = HQLicense::findOrFail($id);
        $this->licenseService->suspendLicense($license);
        
        return back()->with('success', 'License suspended.');
    }

    public function toggleFeature(Request $request, $id)
    {
        Gate::authorize('hq.manageLicense');
        
        $license = HQLicense::findOrFail($id);
        $featureName = $request->input('feature_name');
        $enabled = $request->boolean('enabled');
        
        if ($enabled) {
            $this->licenseService->enableFeature($license, $featureName);
        } else {
            $this->licenseService->disableFeature($license, $featureName);
        }
        
        return back()->with('success', 'Feature toggled successfully.');
    }
}
