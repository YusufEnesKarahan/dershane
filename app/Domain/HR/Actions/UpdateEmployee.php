<?php

namespace App\Domain\HR\Actions;

use App\Domain\HR\Services\HRService;
use App\DTOs\HR\UpdateEmployeeDTO;

class UpdateEmployee
{
    public function __construct(
        protected HRService $hrService
    ) {}

    public function execute(int $id, UpdateEmployeeDTO $dto)
    {
        return $this->hrService->updateEmployee($id, $dto);
    }
}
