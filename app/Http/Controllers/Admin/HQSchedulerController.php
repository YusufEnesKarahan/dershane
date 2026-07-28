<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQSchedulerLog;

class HQSchedulerController extends Controller
{
    public function index()
    {
        $metrics = [
            'scheduler_enabled' => config('hq.scheduler.enabled'),
            'last_telemetry' => HQSchedulerLog::where('task_name', 'hq:telemetry')->where('status', 'success')->latest('finished_at')->first(),
            'last_heartbeat' => HQSchedulerLog::where('task_name', 'hq:heartbeat')->where('status', 'success')->latest('finished_at')->first(),
            'failed_tasks' => HQSchedulerLog::where('status', 'failed')->count(),
        ];

        $logs = HQSchedulerLog::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.platform.scheduler.index', compact('metrics', 'logs'));
    }
}
