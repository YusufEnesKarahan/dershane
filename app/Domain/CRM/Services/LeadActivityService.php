<?php

namespace App\Domain\CRM\Services;

use App\Core\Repositories\Interfaces\LeadActivityRepositoryInterface;
use App\Models\LeadActivity;

class LeadActivityService
{
    public function __construct(protected LeadActivityRepositoryInterface $activityRepo) {}

    public function getTimeline(int $leadId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->activityRepo->getTimeline($leadId);
    }

    public function logActivity(int $leadId, string $actionType, string $description, ?int $userId = null): LeadActivity
    {
        return $this->activityRepo->log($leadId, $actionType, $description, $userId);
    }
}
