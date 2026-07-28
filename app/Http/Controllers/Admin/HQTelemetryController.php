<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQTelemetryLog;
use App\Domain\Platform\Services\HQTelemetryService;
use App\Domain\Platform\Services\HQHttpService;

class HQTelemetryController extends Controller
{
    public function __construct(
        protected HQTelemetryService $telemetryService,
        protected HQHttpService $hqHttpService
    ) {}

    public function index()
    {
        $currentHealth = $this->telemetryService->collectHealth();
        $currentSystem = $this->telemetryService->collectSystem();
        $currentUsage = $this->telemetryService->collectUsage();
        
        $logs = HQTelemetryLog::orderBy('created_at', 'desc')->paginate(20);
        $lastLog = HQTelemetryLog::latest('created_at')->first();
        
        return view('admin.platform.telemetry.index', compact('currentHealth', 'currentSystem', 'currentUsage', 'logs', 'lastLog'));
    }

    public function send()
    {
        $snapshot = $this->telemetryService->createSnapshot();
        
        // Log telemetry locally
        $log = $this->telemetryService->storeSnapshot($snapshot);
        
        // Send to HQ
        $response = $this->hqHttpService->sendTelemetry($snapshot);
        
        if (isset($response['success']) && $response['success']) {
            return redirect()->route('admin.platform.telemetry.index')->with('success', 'Telemetry verisi HQ\'ya başarıyla gönderildi.');
        } else {
            $log->update(['status' => 'failed']);
            return redirect()->route('admin.platform.telemetry.index')->with('error', 'Telemetry verisi gönderilirken hata oluştu: ' . ($response['error'] ?? 'Bilinmeyen hata'));
        }
    }
}
