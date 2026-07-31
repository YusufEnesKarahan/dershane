<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\HQSubscription;
use App\Models\HQPlan;

class SubscriptionUpgraded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $subscription;
    public $oldPlan;

    /**
     * Create a new event instance.
     */
    public function __construct(HQSubscription $subscription, HQPlan $oldPlan)
    {
        $this->subscription = $subscription;
        $this->oldPlan = $oldPlan;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
