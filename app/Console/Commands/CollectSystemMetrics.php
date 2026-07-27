<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SystemMetric;

class CollectSystemMetrics extends Command
{
    protected $signature = 'system:collect-metrics';
    protected $description = 'Collects and saves daily lightweight system metrics';

    public function handle(): int
    {
        $this->info('Collecting system metrics...');

        $metrics = [
            'active_users' => User::count(),
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'failed_queue_jobs' => DB::table('failed_jobs')->count(),
        ];

        foreach ($metrics as $name => $value) {
            SystemMetric::create([
                'metric_name' => $name,
                'metric_value' => (double) $value,
                'metadata' => [
                    'collected_at' => now()->toIso8601String()
                ],
                'created_at' => now(),
            ]);
        }

        $this->info('Metrics successfully collected and stored.');
        return Command::SUCCESS;
    }
}
