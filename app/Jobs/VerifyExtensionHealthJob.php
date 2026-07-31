<?php

namespace App\Jobs;

use App\Models\HQExtensionInstallation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyExtensionHealthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $installations = HQExtensionInstallation::with(['extension', 'version'])->whereIn('status', ['activated', 'failed'])->get();
        
        $dependencyService = app(\App\Domain\HQ\Services\Extension\ExtensionDependencyService::class);
        $installationService = app(\App\Domain\HQ\Services\Extension\ExtensionInstallationService::class);

        foreach ($installations as $installation) {
            $healthOk = true; 
            
            // 1. Dependency / Version check
            if ($installation->version) {
                $compatibility = $dependencyService->checkCompatibility($installation->version, ['php' => '8.4', 'hq_central' => '8.8']);
                if (!$compatibility['is_compatible']) {
                    $healthOk = false;
                }
            } else {
                $healthOk = false; // Version missing
            }

            // 2. Failed installation check
            if ($installation->status === 'failed') {
                $healthOk = false;
            }

            if (!$healthOk && $installation->status === 'activated') {
                // Disable it
                $installationService->disable($installation);
                
                \Log::warning("Extension installation {$installation->id} failed health check and was disabled.");
                
                app(\App\Domain\HQ\Services\HQAlertService::class)->createAlert(
                    severity: 'warning',
                    title: 'extension.health.failed',
                    message: "Extension {$installation->extension->slug} failed health check and was disabled.",
                    metadata: ['installation_id' => $installation->id]
                );
            }
        }
    }
}
