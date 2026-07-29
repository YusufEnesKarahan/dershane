<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Domain\HQ\Services\TenantService;

class HQTenantController extends Controller
{
    public function __construct(
        protected TenantService $tenantService
    ) {}

    public function index()
    {
        Gate::authorize('hq.manageTenant');
        
        $tenants = HQTenant::withCount('instances')->orderBy('name')->paginate(20);
        return view('admin.hq.tenants.index', compact('tenants'));
    }

    public function store(Request $request)
    {
        Gate::authorize('hq.manageTenant');
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:hq_tenants,slug',
            'status' => 'nullable|string|in:active,suspended,disabled',
        ]);
        
        $this->tenantService->createTenant($data);
        
        return redirect()->route('admin.platform.hq_central.tenants.index')->with('success', 'Tenant created successfully.');
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('hq.manageTenant');
        
        $tenant = HQTenant::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:hq_tenants,slug,'.$tenant->id,
            'status' => 'required|string|in:active,suspended,disabled',
        ]);
        
        $tenant->update($data);
        
        return redirect()->route('admin.platform.hq_central.tenants.index')->with('success', 'Tenant updated successfully.');
    }
}
