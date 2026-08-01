<?php

namespace App\Jobs;

use App\Core\Services\Extension\ExtensionInstallationService;
use App\Models\HQExtensionInstallation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveExtensionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $installation;

    public function __construct(HQExtensionInstallation $installation)
    {
        $this->installation = $installation;
    }

    public function handle(ExtensionInstallationService $installationService)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($installationService) {
            $installationService->uninstall($this->installation);
        });
    }

    public function failed(\Throwable $exception)
    {
        \Log::error("Failed to remove extension via Queue: " . $exception->getMessage());
    }
}
