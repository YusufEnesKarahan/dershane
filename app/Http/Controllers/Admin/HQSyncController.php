<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Platform\Services\HQSyncService;
use App\Models\HQSyncEvent;

class HQSyncController extends Controller
{
    public function __construct(protected HQSyncService $hqSyncService) {}

    public function index()
    {
        $metrics = [
            'pending' => $this->hqSyncService->pending(),
            'completed' => $this->hqSyncService->completed(),
            'failed' => $this->hqSyncService->failed(),
        ];

        $events = HQSyncEvent::orderBy('created_at', 'desc')->take(20)->get();

        return view('admin.platform.sync.index', compact('metrics', 'events'));
    }
}
