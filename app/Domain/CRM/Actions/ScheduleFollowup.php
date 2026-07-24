<?php

namespace App\Domain\CRM\Actions;

use App\DTOs\CRM\CreateFollowupDTO;
use App\Domain\CRM\Services\FollowupService;
use App\Models\LeadFollowup;

class ScheduleFollowup
{
    public function __construct(protected FollowupService $service) {}

    public function execute(CreateFollowupDTO $dto): LeadFollowup
    {
        return $this->service->createFollowup($dto);
    }
}
