<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\LeadActivity;

interface LeadActivityRepositoryInterface
{
    public function log(int $leadId, string $actionType, string $description, ?int $userId): LeadActivity;

    public function getTimeline(int $leadId): \Illuminate\Database\Eloquent\Collection;
}
