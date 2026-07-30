<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQBackupSnapshot;
use App\Models\HQSystemInstance;
use App\Domain\HQ\Services\Backup\RestoreService;

class HQRestoreApiController extends Controller
{
    /**
     * POST /api/hq/restore/start
     */
    public function start(Request $request, RestoreService $service)
    {
        $snapshotId = $request->input('snapshot_id');
        $instanceId = $request->input('target_instance_id');
        $mode = $request->input('mode', 'execute'); // dry_run, validation, execute
        $type = $request->input('type', 'specific');

        $snapshot = HQBackupSnapshot::findOrFail($snapshotId);
        $instance = HQSystemInstance::findOrFail($instanceId);

        try {
            $job = $service->startRestore($snapshot, $instance, $mode, $type);
            return response()->json([
                'status' => 'success',
                'job_id' => $job->id,
                'message' => 'Restore job queued successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
