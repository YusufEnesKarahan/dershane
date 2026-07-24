<?php

namespace App\Domain\Admission\Actions;

use App\DTOs\Admission\CreateAdmissionDTO;
use App\Domain\Admission\Services\AdmissionService;
use App\Models\StudentAdmission;

class CreateAdmission
{
    public function __construct(protected AdmissionService $service) {}

    public function execute(CreateAdmissionDTO $dto): StudentAdmission
    {
        return $this->service->createAdmission($dto);
    }
}
