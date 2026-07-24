<?php

namespace App\Domain\CRM\Actions;

use App\DTOs\CRM\LeadNoteDTO;
use App\Domain\CRM\Services\LeadService;
use App\Models\LeadNote;

class AddLeadNote
{
    public function __construct(protected LeadService $service) {}

    public function execute(LeadNoteDTO $dto): LeadNote
    {
        return $this->service->addNote($dto);
    }
}
