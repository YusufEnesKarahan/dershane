<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQBackupPolicy;
use App\Models\HQBackupJob;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Domain\HQ\Services\HQBackupService;

class HQBackupController extends Controller
{
    public function __construct(
        protected HQBackupService $backupService
    ) {}

    public function index()
    {
        $stats = [
            'total_policies' => HQBackupPolicy::count(),
            'successful_jobs' => HQBackupJob::where('status', 'completed')->count(),
            'failed_jobs' => HQBackupJob::where('status', 'failed')->count(),
            'total_storage' => HQBackupJob::where('status', 'completed')->sum('size') ?? 0,
        ];

        $recentJobs = HQBackupJob::with(['policy', 'systemInstance'])->latest()->take(10)->get();

        return view('admin.hq.backups.index', compact('stats', 'recentJobs'));
    }

    public function policies()
    {
        $policies = HQBackupPolicy::with(['tenant', 'systemInstance'])->latest()->paginate(20);
        return view('admin.hq.backups.policies', compact('policies'));
    }

    public function create()
    {
        $tenants = HQTenant::active()->get();
        $instances = HQSystemInstance::with('tenant')->get();

        return view('admin.hq.backups.create', compact('tenants', 'instances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tenant_id' => 'nullable|exists:hq_tenants,id',
            'system_instance_id' => 'nullable|exists:hq_system_instances,id',
            'frequency' => 'required|in:daily,weekly,monthly',
            'retention_days' => 'required|integer|min:1|max:365',
            'backup_type' => 'required|in:database,files,full',
            'is_active' => 'boolean',
        ]);

        $this->backupService->createPolicy($validated);

        return redirect()->route('admin.platform.hq_central.backups.policies')
                         ->with('success', 'Backup policy created successfully.');
    }

    public function show(HQBackupJob $job)
    {
        $job->load(['policy', 'systemInstance', 'logs']);
        return view('admin.hq.backups.show', compact('job'));
    }

    public function retry(HQBackupJob $job)
    {
        try {
            $this->backupService->retryFailedBackup($job);
            return back()->with('success', 'Backup job retried successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
