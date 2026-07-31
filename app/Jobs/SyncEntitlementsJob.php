<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HQSubscription;
use App\Domain\HQ\Services\Billing\EntitlementService;

class SyncEntitlementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subscription;

    public function __construct(HQSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function handle(EntitlementService $entitlementService)
    {
        $entitlementService->syncPlanEntitlements($this->subscription);
    }
}
