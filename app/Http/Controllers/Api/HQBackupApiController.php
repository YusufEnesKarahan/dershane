<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQBackupPolicy;
use App\Models\HQBackupJob;
use App\Models\HQBackupStorageLocation;
use App\Domain\HQ\Services\Backup\BackupOrchestrationService;

class HQBackupApiController extends Controller
{
    /**
     * POST /api/hq/backup/start
     */
    public function start(Request $request, BackupOrchestrationService $service)
    {
        $policyId = $request->input('policy_id');
        $policy = HQBackupPolicy::findOrFail($policyId);

        try {
            $job = $service->startBackup($policy);
            return response()->json([
                'status' => 'success',
                'job_id' => $job->id,
                'message' => 'Backup job queued successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * POST /api/hq/backup/report
     */
    public function report(Request $request, BackupOrchestrationService $service)
    {
        $jobId = $request->input('job_id');
        $status = $request->input('status');
        
        $job = HQBackupJob::findOrFail($jobId);

        if ($status === 'completed') {
            $size = $request->input('size_bytes');
            $path = $request->input('path');
            $type = $request->input('snapshot_type', 'full');
            $service->completeBackup($job, $size, $path, $type);
        } else {
            $error = $request->input('error_message', 'Unknown error');
            $service->failBackup($job, $error);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Backup report processed.'
        ]);
    }

    /**
     * GET /api/hq/backup/status
     */
    public function status(Request $request)
    {
        $jobId = $request->query('job_id');
        if ($jobId) {
            $job = HQBackupJob::findOrFail($jobId);
            return response()->json(['status' => 'success', 'data' => $job]);
        }
        
        $jobs = HQBackupJob::latest()->limit(50)->get();
        return response()->json(['status' => 'success', 'data' => $jobs]);
    }

    /**
     * GET /api/hq/backup/policies
     */
    public function policies(Request $request)
    {
        $policies = HQBackupPolicy::with('storageLocation')->get();
        return response()->json(['status' => 'success', 'data' => $policies]);
    }

    /**
     * GET /api/hq/storage
     */
    public function storage(Request $request)
    {
        $storage = HQBackupStorageLocation::all();
        return response()->json(['status' => 'success', 'data' => $storage]);
    }
}
