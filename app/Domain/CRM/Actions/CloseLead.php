<?php

namespace App\Domain\CRM\Actions;

use App\Domain\CRM\Services\LeadPipelineService;
use App\Models\LeadStatus;

class CloseLead
{
    public function __construct(protected LeadPipelineService $service) {}

    public function execute(int $leadId, ?int $userId = null): bool
    {
        $statusId = LeadStatus::where('code', 'LOST')->value('id');
        if (!$statusId) {
            return false;
        }
        return $this->service->moveLead($leadId, $statusId, $userId);
    }
}
