<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQBackupPolicy;
use App\Models\HQBackupJob;
use App\Models\HQBackupSnapshot;
use App\Models\HQBackupRestoreJob;
use App\Models\HQBackupStorageLocation;
use App\Models\HQDisasterRecoveryPlan;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Domain\HQ\Services\Backup\BackupHealthService;

class HQBackupController extends Controller
{
    public function __construct(
        protected BackupHealthService $healthService
    ) {}

    public function index()
    {
        $stats = $this->healthService->getMetrics();
        $recentJobs = HQBackupJob::with(['policy', 'systemInstance'])->latest()->take(10)->get();

        return view('admin.hq.backups.index', compact('stats', 'recentJobs'));
    }

    public function policies()
    {
        $policies = HQBackupPolicy::with(['tenant', 'systemInstance', 'storageLocation'])->latest()->paginate(20);
        return view('admin.hq.backups.policies', compact('policies'));
    }

    public function jobs()
    {
        $jobs = HQBackupJob::with(['policy', 'systemInstance'])->latest()->paginate(20);
        return view('admin.hq.backups.jobs', compact('jobs'));
    }

    public function snapshots()
    {
        $snapshots = HQBackupSnapshot::with(['job.systemInstance'])->latest()->paginate(20);
        return view('admin.hq.backups.snapshots', compact('snapshots'));
    }

    public function restores()
    {
        $restores = HQBackupRestoreJob::with(['targetInstance', 'snapshot'])->latest()->paginate(20);
        return view('admin.hq.backups.restores', compact('restores'));
    }

    public function storage()
    {
        $locations = HQBackupStorageLocation::latest()->paginate(20);
        return view('admin.hq.backups.storage', compact('locations'));
    }

    public function drPlans()
    {
        $plans = HQDisasterRecoveryPlan::with('tenant')->latest()->paginate(20);
        return view('admin.hq.backups.dr_plans', compact('plans'));
    }

    public function createPolicy()
    {
        $tenants = HQTenant::active()->get();
        $instances = HQSystemInstance::with('tenant')->get();
        $storage = HQBackupStorageLocation::active()->get();

        return view('admin.hq.backups.create_policy', compact('tenants', 'instances', 'storage'));
    }
}
