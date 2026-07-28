<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQUpdate;
use App\Domain\Platform\Services\HQUpdateService;

class HQUpdateController extends Controller
{
    public function __construct(
        protected HQUpdateService $updateService
    ) {}

    public function index()
    {
        $currentVersion = $this->updateService->currentVersion();
        $latestUpdate = $this->updateService->getLatest();
        $updates = HQUpdate::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.platform.updates.index', compact('currentVersion', 'latestUpdate', 'updates'));
    }

    public function check()
    {
        if (!config('hq.updates.enabled')) {
            return redirect()->route('admin.platform.updates.index')->with('error', 'Updates module is disabled in configuration.');
        }

        $update = $this->updateService->checkAvailable();

        if ($update) {
            return redirect()->route('admin.platform.updates.index')->with('success', 'New update found: v' . $update['version']);
        }

        return redirect()->route('admin.platform.updates.index')->with('success', 'System is up to date.');
    }

    public function markInstalled(HQUpdate $update)
    {
        $this->updateService->markInstalled($update);
        return redirect()->route('admin.platform.updates.index')->with('success', 'Update marked as installed manually.');
    }
}
