<?php

namespace App\Jobs;

use App\Domain\HQ\Services\Extension\ExtensionInstallationService;
use App\Models\HQExtension;
use App\Models\HQExtensionVersion;
use App\Models\HQTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InstallExtensionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $extension;
    public $version;
    public $tenant;

    public function __construct(HQExtension $extension, HQExtensionVersion $version, HQTenant $tenant)
    {
        $this->extension = $extension;
        $this->version = $version;
        $this->tenant = $tenant;
    }

    public function handle(ExtensionInstallationService $installationService)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($installationService) {
            $installation = $installationService->install($this->extension, $this->version, $this->tenant);
            $installationService->enable($installation);
        });
    }

    public function failed(\Throwable $exception)
    {
        \Log::error("Failed to install extension via Queue: " . $exception->getMessage());
        
        event(new \App\Events\ExtensionInstallationFailed(
            new \App\Models\HQExtensionInstallation(['extension_id' => $this->extension->id, 'tenant_id' => $this->tenant->id]), 
            $exception->getMessage()
        ));
    }
}
