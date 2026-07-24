<?php

namespace App\Domain\CRM\Actions;

use App\DTOs\CRM\AssignLeadDTO;
use App\Domain\CRM\Services\LeadAssignmentService;

class AssignLead
{
    public function __construct(protected LeadAssignmentService $service) {}

    public function execute(AssignLeadDTO $dto): bool
    {
        return $this->service->assignLead($dto);
    }
}
