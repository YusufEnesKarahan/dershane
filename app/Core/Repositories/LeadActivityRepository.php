<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\LeadActivityRepositoryInterface;
use App\Models\LeadActivity;

class LeadActivityRepository implements LeadActivityRepositoryInterface
{
    public function log(int $leadId, string $actionType, string $description, ?int $userId): LeadActivity
    {
        return LeadActivity::create([
            'lead_id' => $leadId,
            'action_type' => $actionType,
            'description' => $description,
            'user_id' => $userId,
        ]);
    }

    public function getTimeline(int $leadId): \Illuminate\Database\Eloquent\Collection
    {
        return LeadActivity::with('user')->where('lead_id', $leadId)->orderBy('created_at', 'desc')->get();
    }
}
