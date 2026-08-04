<?php

namespace App\Domain\Package\Services;

use App\Domain\Package\Models\Package;
use App\Domain\Package\Models\Feature;
use App\Domain\Package\Models\BranchPackage;
use App\Models\Branch;
use App\Core\Context\TenantContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PackageService
{
    /**
     * Get active package for a branch.
     */
    public function getActivePackage($branch = null): ?Package
    {
        $branchId = $this->resolveBranchId($branch);

        if (!$branchId) {
            return null;
        }

        $branchPackage = BranchPackage::where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->first();

        if ($branchPackage && $branchPackage->isActive()) {
            return $branchPackage->package;
        }

        // Fallback: If no branch package is assigned, treat as V3 (full access)
        return Package::where('code', 'V3')->first();
    }

    /**
     * Check if a feature is enabled for a given branch.
     */
    public function hasFeature($branch, string $featureCode): bool
    {
        // Super admin always has access to all features
        if (auth()->check() && auth()->user()->isAdministrator()) {
            return true;
        }

        $branchId = $this->resolveBranchId($branch);
        
        // If no branch context exists, allow access
        if (!$branchId) {
            return true;
        }

        $package = $this->getActivePackage($branchId);

        if (!$package) {
            return true; // Fallback: full access
        }

        return $package->features->contains('code', strtolower($featureCode));
    }

    /**
     * Assign or change the package for a branch.
     */
    public function changeBranchPackage(
        $branch,
        int $packageId,
        string $licenseType = 'yearly',
        ?string $startDate = null,
        ?string $endDate = null
    ): BranchPackage {
        $branchId = $this->resolveBranchId($branch);

        if (!$branchId) {
            throw new \InvalidArgumentException('Valid branch is required to assign a package.');
        }

        // Deactivate previous active packages for this branch
        BranchPackage::where('branch_id', $branchId)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        $start = $startDate ? \Carbon\Carbon::parse($startDate) : now();
        $end = $endDate ? \Carbon\Carbon::parse($endDate) : ($licenseType === 'three_year' ? $start->copy()->addYears(3) : $start->copy()->addYear());

        $branchPackage = BranchPackage::create([
            'branch_id' => $branchId,
            'package_id' => $packageId,
            'license_type' => $licenseType,
            'start_date' => $start,
            'end_date' => $end,
            'status' => 'active',
        ]);

        return $branchPackage->load('package.features');
    }

    /**
     * List all packages with features loaded.
     */
    public function listPackages(): Collection
    {
        return Package::with('features')->get();
    }

    /**
     * List all available features.
     */
    public function listFeatures(): Collection
    {
        return Feature::where('status', 'active')->get();
    }

    /**
     * Get features of a specific package.
     */
    public function getPackageFeatures(int $packageId): Collection
    {
        $package = Package::with('features')->find($packageId);
        return $package ? $package->features : collect();
    }

    /**
     * Resolve branch_id from Branch model, int, or active tenant context.
     */
    protected function resolveBranchId($branch = null): ?int
    {
        if ($branch instanceof Branch) {
            return $branch->id;
        }

        if (is_numeric($branch)) {
            return (int) $branch;
        }

        if (auth()->check() && auth()->user()->branch_id) {
            return auth()->user()->branch_id;
        }

        return TenantContext::getActiveBranchId();
    }
}
