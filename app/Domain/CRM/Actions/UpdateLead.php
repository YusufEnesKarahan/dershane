<?php

namespace App\Domain\CRM\Actions;

use App\DTOs\CRM\UpdateLeadDTO;
use App\Domain\CRM\Services\LeadService;

class UpdateLead
{
    public function __construct(protected LeadService $service) {}

    public function execute(int $id, UpdateLeadDTO $dto): bool
    {
        return $this->service->updateLead($id, $dto);
    }
}
