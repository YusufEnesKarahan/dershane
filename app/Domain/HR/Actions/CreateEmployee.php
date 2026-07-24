<?php

namespace App\Domain\HR\Actions;

use App\Domain\HR\Services\HRService;
use App\DTOs\HR\CreateEmployeeDTO;

class CreateEmployee
{
    public function __construct(
        protected HRService $hrService
    ) {}

    public function execute(CreateEmployeeDTO $dto)
    {
        return $this->hrService->createEmployee($dto);
    }
}
