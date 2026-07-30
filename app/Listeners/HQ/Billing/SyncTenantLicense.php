<?php

namespace App\Listeners\HQ\Billing;

use App\Events\HQ\Billing\SubscriptionCreated;
use App\Events\HQ\Billing\SubscriptionUpgraded;
use App\Events\HQ\Billing\SubscriptionCancelled;
use App\Events\HQ\Billing\SubscriptionExpired;
use App\Models\HQLicense;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncTenantLicense implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $subscription = $event->subscription;
        $tenant = $subscription->tenant;

        if ($event instanceof SubscriptionCancelled || $event instanceof SubscriptionExpired) {
            // Revoke license
            HQLicense::where('tenant_id', $tenant->id)->update([
                'status' => 'expired',
            ]);
            return;
        }

        if ($event instanceof SubscriptionCreated || $event instanceof SubscriptionUpgraded) {
            $plan = $subscription->plan;
            
            // Find existing active license or create a new one
            $license = HQLicense::where('tenant_id', $tenant->id)
                ->whereIn('status', ['active', 'expired'])
                ->first();

            if (!$license) {
                $license = new HQLicense([
                    'tenant_id' => $tenant->id,
                ]);
            }

            $metadata = $license->metadata ?? [];
            $metadata['limits'] = $plan->limits;

            $license->fill([
                'plan' => $plan->slug,
                'status' => 'active',
                'starts_at' => $subscription->starts_at,
                'expires_at' => $subscription->ends_at,
                'features' => $plan->features,
                'metadata' => $metadata,
            ]);

            $license->save();
        }
    }
}
