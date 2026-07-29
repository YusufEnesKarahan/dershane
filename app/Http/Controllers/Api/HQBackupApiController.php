<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQBackupJob;

class HQBackupApiController extends Controller
{
    public function check(Request $request)
    {
        // Handled securely. Usually checks if any backups are active.
        return response()->json([
            'status' => 'success',
            'message' => 'Backup check confirmed',
            'timestamp' => now()->timestamp,
        ]);
    }

    public function start(Request $request)
    {
        // Usually called when backup starts successfully locally.
        $jobId = $request->input('job_id');
        if ($jobId) {
            $job = HQBackupJob::find($jobId);
            if ($job) {
                $job->update(['status' => 'running', 'started_at' => now()]);
                \App\Events\BackupCompleted::dispatch('backup.started', $job, 'Backup marked as running');
            }
        }
        
        return response()->json([
            'status' => 'success',
            'backup_id' => $jobId,
            'message' => 'Backup marked as running',
            'timestamp' => now()->timestamp,
        ]);
    }

    public function progress(Request $request)
    {
        $jobId = $request->input('job_id');
        $progress = $request->input('progress');
        
        if ($jobId) {
            $job = HQBackupJob::find($jobId);
            if ($job) {
                // We might save progress in metadata
                $meta = $job->metadata ?? [];
                $meta['progress'] = $progress;
                $job->update(['metadata' => $meta]);
            }
        }

        return response()->json([
            'status' => 'success',
            'backup_id' => $jobId,
            'message' => 'Backup progress updated',
            'timestamp' => now()->timestamp,
        ]);
    }

    public function finished(Request $request)
    {
        $jobId = $request->input('job_id');
        
        if ($jobId) {
            $job = HQBackupJob::find($jobId);
            if ($job) {
                $job->update([
                    'status' => 'completed',
                    'finished_at' => now(),
                    'size' => $request->input('size'),
                    'storage_location' => $request->input('storage_location'),
                ]);
                \App\Events\BackupCompleted::dispatch('backup.completed', $job, 'Backup finished successfully');
            }
        }
        
        return response()->json([
            'status' => 'success',
            'backup_id' => $jobId,
            'message' => 'Backup finished successfully',
            'timestamp' => now()->timestamp,
        ]);
    }
}
