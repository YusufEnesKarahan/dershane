<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Package\Models\Package;
use App\Domain\Package\Models\Feature;
use App\Domain\Package\Models\BranchPackage;
use App\Domain\Package\Services\PackageService;
use App\Models\Branch;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct(
        protected PackageService $packageService
    ) {}

    /**
     * Display list of all SaaS packages for admin management.
     */
    public function index()
    {
        $packages = Package::with('features')->get();
        $features = Feature::where('status', 'active')->get();

        return view('admin.packages.index', compact('packages', 'features'));
    }

    /**
     * Show form to create a new package.
     */
    public function create()
    {
        $features = Feature::where('status', 'active')->get();
        return view('admin.packages.create', compact('features'));
    }

    /**
     * Store a newly created package.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:packages,code',
            'description' => 'nullable|string',
            'price_yearly' => 'required|numeric|min:0',
            'price_3_year' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id',
        ]);

        $package = Package::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'price_yearly' => $validated['price_yearly'],
            'price_3_year' => $validated['price_3_year'],
            'status' => $validated['status'],
        ]);

        if (isset($validated['features'])) {
            $package->features()->sync($validated['features']);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'Yeni paket başarıyla oluşturuldu.');
    }

    /**
     * Show form to edit package and assign features.
     */
    public function edit(Package $package)
    {
        $features = Feature::where('status', 'active')->get();
        $package->load('features');

        return view('admin.packages.edit', compact('package', 'features'));
    }

    /**
     * Update specified package and its assigned features.
     */
    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:packages,code,' . $package->id,
            'description' => 'nullable|string',
            'price_yearly' => 'required|numeric|min:0',
            'price_3_year' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id',
        ]);

        $package->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'price_yearly' => $validated['price_yearly'],
            'price_3_year' => $validated['price_3_year'],
            'status' => $validated['status'],
        ]);

        $package->features()->sync($validated['features'] ?? []);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket ve özellikleri başarıyla güncellendi.');
    }

    /**
     * Toggle active/inactive status of a package.
     */
    public function toggleStatus(Package $package)
    {
        $newStatus = $package->status === 'active' ? 'inactive' : 'active';
        $package->update(['status' => $newStatus]);

        return back()->with('success', "Paket durumu '{$newStatus}' olarak değiştirildi.");
    }

    /**
     * View active branch package details.
     */
    public function branchPackage()
    {
        $activeBranchId = session('active_branch_id', auth()->user()->branch_id);
        $branch = $activeBranchId ? Branch::find($activeBranchId) : null;
        
        $activeBranchPackage = BranchPackage::where('branch_id', $activeBranchId)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->first();

        $activePackage = $activeBranchPackage ? $activeBranchPackage->package->load('features') : $this->packageService->getActivePackage($activeBranchId);
        $allPackages = Package::where('status', 'active')->with('features')->get();

        return view('admin.packages.branch', compact('branch', 'activeBranchPackage', 'activePackage', 'allPackages'));
    }

    /**
     * Assign or upgrade branch package.
     */
    public function assignBranchPackage(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'package_id' => 'required|exists:packages,id',
            'license_type' => 'required|in:yearly,three_year',
        ]);

        $this->packageService->changeBranchPackage(
            $validated['branch_id'],
            $validated['package_id'],
            $validated['license_type']
        );

        return back()->with('success', 'Şube paketi başarıyla güncellendi.');
    }
}
