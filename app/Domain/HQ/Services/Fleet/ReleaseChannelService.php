<?php

namespace App\Domain\HQ\Services\Fleet;

use App\Models\HQReleaseChannel;
use App\Models\HQTenant;

class ReleaseChannelService
{
    /**
     * Get all active release channels.
     */
    public function getChannels()
    {
        return HQReleaseChannel::all();
    }

    /**
     * Assign a tenant to a release channel.
     */
    public function assignTenantToChannel(HQTenant $tenant, HQReleaseChannel $channel): bool
    {
        $tenant->update(['hq_release_channel_id' => $channel->id]);
        
        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'fleet.channel.assigned',
            category: 'fleet',
            severity: 'info',
            description: "Tenant {$tenant->name} assigned to channel {$channel->name}.",
            tenantId: $tenant->id,
            metadata: ['channel_id' => $channel->id]
        );

        return true;
    }

    /**
     * Get target instances for a channel.
     */
    public function getInstancesInChannel(HQReleaseChannel $channel)
    {
        return \App\Models\HQSystemInstance::whereIn('tenant_id', function ($query) use ($channel) {
            $query->select('id')->from('hq_tenants')->where('hq_release_channel_id', $channel->id);
        })->get();
    }
}
