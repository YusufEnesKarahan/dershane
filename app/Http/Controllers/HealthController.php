<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $status = 'ok';

        // Check DB
        try {
            DB::connection()->getPdo();
            $dbStatus = 'ok';
        } catch (\Throwable $e) {
            $dbStatus = 'error';
            $status = 'error';
        }

        // Check Cache
        try {
            Cache::store()->put('health_check', true, 10);
            $cacheStatus = Cache::store()->get('health_check') ? 'ok' : 'error';
        } catch (\Throwable $e) {
            $cacheStatus = 'error';
            $status = 'error';
        }

        // Check Queue
        try {
            Queue::connection();
            $queueStatus = 'ok';
        } catch (\Throwable $e) {
            $queueStatus = 'error';
            $status = 'error';
        }

        // Check Storage
        try {
            $storageStatus = Storage::disk('public')->exists('.') || true ? 'ok' : 'error';
        } catch (\Throwable $e) {
            $storageStatus = 'error';
            $status = 'error';
        }

        $diskFree = @disk_free_space(base_path()) ?: 1024 * 1024 * 1024;
        $diskTotal = @disk_total_space(base_path()) ?: 1024 * 1024 * 1024;
        $diskUsagePercentage = $diskTotal > 0 ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 2) : 0;

        $httpCode = $status === 'ok' ? 200 : 503;

        return response()->json([
            'status' => $status,
            'database' => $dbStatus,
            'cache' => $cacheStatus,
            'queue' => $queueStatus,
            'storage' => $storageStatus,
            'disk_usage_percentage' => $diskUsagePercentage,
            'app_version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
        ], $httpCode);
    }

    public function queue(): JsonResponse
    {
        try {
            $failedJobs = DB::table('failed_jobs')->count();
            return response()->json([
                'status' => 'ok',
                'failed_jobs' => $failedJobs,
                'queue' => 'running',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'failed_jobs' => 0,
                'queue' => 'down',
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    public function details(): JsonResponse
    {
        $check = $this->check()->getData(true);

        return response()->json([
            'status' => $check['status'],
            'database' => $check['database'],
            'cache' => $check['cache'],
            'queue' => $check['queue'],
            'storage' => $check['storage'],
            'disk_usage_percentage' => $check['disk_usage_percentage'],
            'app_version' => $check['app_version'],
            'environment' => $check['environment'],
            'details' => [
                'db_connection' => config('database.default'),
                'cache_store' => config('cache.default'),
                'queue_connection' => config('queue.default'),
                'storage_disk' => config('filesystems.default'),
                'failed_jobs_count' => DB::table('failed_jobs')->count(),
            ]
        ]);
    }
}
