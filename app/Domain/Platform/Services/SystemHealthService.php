<?php

namespace App\Domain\Platform\Services;

use App\Models\HQSchedulerLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SystemHealthService
{
    public function getDashboardMetrics(): array
    {
        $database = $this->checkDatabase();
        $storage = $this->checkStorage();
        $queue = $this->checkQueue();

        return [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
            'cache_driver' => config('cache.default', 'file'),
            'queue' => $queue,
            'storage' => $storage,
            'database' => $database,
            'last_successful_cron_at' => $this->getLastSuccessfulCronAt(),
            'overall_status' => $this->resolveOverallStatus($database, $storage, $queue),
        ];
    }

    public function getTenantHealthSummary(): array
    {
        $metrics = $this->getDashboardMetrics();

        return [
            'overall_status' => $metrics['overall_status'],
            'database_status' => $metrics['database']['status'],
            'storage_status' => $metrics['storage']['status'],
            'queue_status' => $metrics['queue']['status'],
            'last_successful_cron_at' => $metrics['last_successful_cron_at'],
        ];
    }

    protected function checkDatabase(): array
    {
        $connectionName = config('database.default');

        try {
            DB::connection()->getPdo();

            return [
                'status' => 'healthy',
                'connection' => $connectionName,
            ];
        } catch (\Throwable $exception) {
            return [
                'status' => 'degraded',
                'connection' => $connectionName,
                'error' => $exception->getMessage(),
            ];
        }
    }

    protected function checkStorage(): array
    {
        $disk = config('filesystems.default', 'local');
        $driver = config("filesystems.disks.{$disk}.driver", $disk);

        try {
            if ($driver === 'local') {
                $root = config("filesystems.disks.{$disk}.root");
                $accessible = $root ? is_dir($root) && is_writable($root) : false;

                return [
                    'status' => $accessible ? 'healthy' : 'degraded',
                    'disk' => $disk,
                    'driver' => $driver,
                    'path' => $root,
                ];
            }

            Storage::disk($disk)->exists('.health-check');

            return [
                'status' => 'healthy',
                'disk' => $disk,
                'driver' => $driver,
            ];
        } catch (\Throwable $exception) {
            return [
                'status' => 'degraded',
                'disk' => $disk,
                'driver' => $driver,
                'error' => $exception->getMessage(),
            ];
        }
    }

    protected function checkQueue(): array
    {
        $driver = config('queue.default', 'sync');
        $status = $driver === 'sync' ? 'degraded' : 'healthy';
        $pendingJobs = null;

        if ($driver === 'database' && Schema::hasTable('jobs')) {
            try {
                $pendingJobs = DB::table('jobs')->count();
            } catch (\Throwable $exception) {
                $pendingJobs = null;
            }
        }

        return [
            'status' => $status,
            'driver' => $driver,
            'pending_jobs' => $pendingJobs,
        ];
    }

    public function getLastSuccessfulCronAt(): ?Carbon
    {
        $finishedAt = HQSchedulerLog::query()
            ->where('status', 'success')
            ->whereNotNull('finished_at')
            ->latest('finished_at')
            ->value('finished_at');

        return $finishedAt ? Carbon::parse($finishedAt) : null;
    }

    protected function resolveOverallStatus(array $database, array $storage, array $queue): string
    {
        if ($database['status'] !== 'healthy' || $storage['status'] !== 'healthy') {
            return 'critical';
        }

        if ($queue['status'] !== 'healthy') {
            return 'warning';
        }

        if (!Cache::has('system.health.ready')) {
            Cache::put('system.health.ready', true, now()->addMinutes(5));
        }

        return 'healthy';
    }
}