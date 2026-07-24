<?php

namespace App\Domain\Admission\Actions;

use App\Domain\Admission\Services\AdmissionService;
use App\Models\StudentAdmission;

class ConvertLeadToAdmission
{
    public function __construct(protected AdmissionService $service) {}

    public function execute(int $leadId, ?int $userId = null): StudentAdmission
    {
        return $this->service->createFromLead($leadId, $userId);
    }
}
