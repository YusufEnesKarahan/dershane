<?php

namespace App\Domain\CRM\Services;

use App\Models\Lead;
use App\Models\LeadStatus;
use App\Core\Repositories\Interfaces\LeadRepositoryInterface;
use App\Core\Repositories\Interfaces\LeadActivityRepositoryInterface;

class LeadPipelineService
{
    public function __construct(
        protected LeadRepositoryInterface $leadRepo,
        protected LeadActivityRepositoryInterface $activityRepo
    ) {}

    public function getPipelineBoard(): array
    {
        $statuses = LeadStatus::orderBy('sort_order', 'asc')->get();
        $board = [];

        foreach ($statuses as $status) {
            $board[] = [
                'status' => $status,
                'leads' => Lead::where('lead_status_id', $status->id)->with(['source', 'advisor'])->get(),
            ];
        }

        return $board;
    }

    public function moveLead(int $leadId, int $statusId, ?int $userId = null): bool
    {
        $lead = Lead::findOrFail($leadId);
        $oldStatus = $lead->status;
        $newStatus = LeadStatus::findOrFail($statusId);

        $updated = $this->leadRepo->updateStatus($leadId, $statusId);

        if ($updated) {
            $oldStatusName = $oldStatus ? $oldStatus->name : 'N/A';
            $this->activityRepo->log(
                $leadId,
                'Updated',
                "Durum güncellendi: {$oldStatusName} -> {$newStatus->name}",
                $userId
            );
        }

        return $updated;
    }
}
