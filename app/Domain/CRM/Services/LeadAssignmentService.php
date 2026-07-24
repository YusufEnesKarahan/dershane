<?php

namespace App\Domain\CRM\Services;

use App\Core\Repositories\Interfaces\LeadRepositoryInterface;
use App\Core\Repositories\Interfaces\LeadActivityRepositoryInterface;
use App\DTOs\CRM\AssignLeadDTO;
use App\Models\LeadAssignment;

class LeadAssignmentService
{
    public function __construct(
        protected LeadRepositoryInterface $leadRepo,
        protected LeadActivityRepositoryInterface $activityRepo
    ) {}

    public function assignLead(AssignLeadDTO $dto): bool
    {
        $lead = $this->leadRepo->find($dto->leadId);
        if (!$lead) {
            return false;
        }

        $oldAdvisorId = $lead->advisor_id;

        $assigned = $this->leadRepo->assign($dto->leadId, $dto->toUserId, $dto->branchId);

        if ($assigned) {
            LeadAssignment::create([
                'lead_id' => $dto->leadId,
                'from_user_id' => $oldAdvisorId,
                'to_user_id' => $dto->toUserId,
                'assigned_at' => now(),
            ]);

            $this->activityRepo->log($dto->leadId, 'Updated', 'Danışman veya şube ataması güncellendi.', $dto->fromUserId);
        }

        return $assigned;
    }
}
