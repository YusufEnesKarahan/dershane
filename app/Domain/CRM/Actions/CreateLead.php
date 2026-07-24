<?php

namespace App\Domain\CRM\Actions;

use App\DTOs\CRM\CreateLeadDTO;
use App\Domain\CRM\Services\LeadService;
use App\Models\Lead;

class CreateLead
{
    public function __construct(protected LeadService $service) {}

    public function execute(CreateLeadDTO $dto): Lead
    {
        return $this->service->createLead($dto);
    }
}
