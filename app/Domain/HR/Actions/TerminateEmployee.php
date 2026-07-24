<?php

namespace App\Domain\HR\Actions;

use App\Domain\HR\Services\HRService;

class TerminateEmployee
{
    public function __construct(
        protected HRService $hrService
    ) {}

    public function execute(int $id)
    {
        return $this->hrService->terminateEmployee($id);
    }
}
