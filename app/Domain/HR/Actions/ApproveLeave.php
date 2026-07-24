<?php

namespace App\Domain\HR\Actions;

use App\Domain\HR\Services\LeaveService;

class ApproveLeave
{
    public function __construct(
        protected LeaveService $leaveService
    ) {}

    public function execute(int $id, int $userId)
    {
        return $this->leaveService->approveRequest($id, $userId);
    }
}
