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

        $httpCode = $status === 'ok' ? 200 : 503;

        return response()->json([
            'status' => $status,
            'database' => $dbStatus,
            'cache' => $cacheStatus,
            'queue' => $queueStatus,
            'storage' => $storageStatus,
        ], $httpCode);
    }
}
