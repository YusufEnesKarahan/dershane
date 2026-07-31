<?php

namespace App\Jobs;

use App\Domain\HQ\Services\Extension\ExtensionInstallationService;
use App\Models\HQExtensionInstallation;
use App\Models\HQExtensionVersion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateExtensionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $installation;
    public $newVersion;

    public function __construct(HQExtensionInstallation $installation, HQExtensionVersion $newVersion)
    {
        $this->installation = $installation;
        $this->newVersion = $newVersion;
    }

    public function handle(ExtensionInstallationService $installationService)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($installationService) {
            $installationService->update($this->installation, $this->newVersion);
        });
    }

    public function failed(\Throwable $exception)
    {
        \Log::error("Failed to update extension via Queue: " . $exception->getMessage());
        
        // Let's transition to failed if it wasn't already caught by the service
        $this->installation->update(['status' => 'failed']);
    }
}
