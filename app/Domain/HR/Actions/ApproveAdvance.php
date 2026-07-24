<?php

namespace App\Domain\HR\Actions;

use App\Domain\HR\Services\AdvanceService;

class ApproveAdvance
{
    public function __construct(
        protected AdvanceService $advanceService
    ) {}

    public function execute(int $id)
    {
        return $this->advanceService->approveAdvance($id);
    }
}
