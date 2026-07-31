<?php

namespace App\Domain\HQ\Services\Extension;

use App\Models\HQExtensionInstallation;
use App\Models\HQTenant;
use Illuminate\Support\Facades\Log;

class ExtensionLifecycleService
{
    /**
     * Transition the state of an installation.
     */
    public function transition(HQExtensionInstallation $installation, string $newStatus)
    {
        $oldStatus = $installation->status;
        $installation->update(['status' => $newStatus]);
        
        Log::info("Extension installation {$installation->id} transitioned from {$oldStatus} to {$newStatus}.");

        // We can hook into specific transitions here if we need synchronous logic,
        // but typically we broadcast events to let the system react asynchronously.
    }
}
