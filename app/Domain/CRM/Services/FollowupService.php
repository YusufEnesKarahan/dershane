<?php

namespace App\Domain\CRM\Services;

use App\DTOs\CRM\CreateFollowupDTO;
use App\Models\LeadFollowup;
use App\Core\Repositories\Interfaces\LeadActivityRepositoryInterface;

class FollowupService
{
    public function __construct(protected LeadActivityRepositoryInterface $activityRepo) {}

    public function getFollowups(): \Illuminate\Database\Eloquent\Collection
    {
        return LeadFollowup::with(['lead', 'user'])->orderBy('followup_date', 'asc')->get();
    }

    public function createFollowup(CreateFollowupDTO $dto): LeadFollowup
    {
        $followup = LeadFollowup::create($dto->toArray());
        $this->activityRepo->log(
            $dto->leadId,
            'Called',
            'Takip arama görevi planlandı: ' . $dto->followupDate . ' - Öncelik: ' . $dto->priority,
            $dto->userId
        );
        return $followup;
    }

    public function completeFollowup(int $id, ?int $userId = null): bool
    {
        $followup = LeadFollowup::findOrFail($id);
        $updated = $followup->update(['status' => 'Completed']);

        if ($updated) {
            $this->activityRepo->log(
                $followup->lead_id,
                'Called',
                'Takip arama görevi tamamlandı: ' . $followup->reminder_note,
                $userId
            );
        }

        return $updated;
    }
}
