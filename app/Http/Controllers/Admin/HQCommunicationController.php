<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Platform\Services\HQHttpService;
use App\Models\HQSyncLog;

class HQCommunicationController extends Controller
{
    public function __construct(protected HQHttpService $hqHttpService) {}

    public function index()
    {
        $logs = HQSyncLog::orderBy('created_at', 'desc')->take(20)->get();
        $hqUrl = config('hq.base_url');
        
        $lastPing = HQSyncLog::where('event_type', 'ping')->where('success', true)->latest('created_at')->first();
        $isConnected = $lastPing && $lastPing->created_at->diffInHours(now()) < 24;

        return view('admin.platform.communication.index', compact('logs', 'hqUrl', 'isConnected'));
    }

    public function ping()
    {
        $response = $this->hqHttpService->ping();
        $type = $response['success'] ? 'success' : 'error';
        return redirect()->route('admin.platform.communication.index')->with($type, 'Ping result: ' . ($response['message'] ?? 'Status ' . $response['status']));
    }

    public function health()
    {
        $response = $this->hqHttpService->health();
        $type = $response['success'] ? 'success' : 'error';
        return redirect()->route('admin.platform.communication.index')->with($type, 'Health sync result: ' . ($response['message'] ?? 'Status ' . $response['status']));
    }

    public function register()
    {
        $response = $this->hqHttpService->register();
        $type = $response['success'] ? 'success' : 'error';
        return redirect()->route('admin.platform.communication.index')->with($type, 'Register result: ' . ($response['message'] ?? 'Status ' . $response['status']));
    }

    public function sync()
    {
        $response = $this->hqHttpService->sync();
        $type = $response['success'] ? 'success' : 'error';
        return redirect()->route('admin.platform.communication.index')->with($type, 'Manual sync result: ' . ($response['message'] ?? 'Status ' . $response['status']));
    }
}
