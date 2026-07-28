<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Platform\Services\UpdateService;

class UpdateController extends Controller
{
    public function __construct(protected UpdateService $updateService) {}

    public function index()
    {
        $currentVersion = $this->updateService->currentVersion();
        $latest = $this->updateService->getLatest();
        $isUpdateAvailable = $this->updateService->isUpdateAvailable();

        return view('admin.platform.updates.index', compact(
            'currentVersion',
            'latest',
            'isUpdateAvailable'
        ));
    }
}
