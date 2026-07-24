<?php

namespace App\Domain\Admission\Actions;

use App\DTOs\Admission\CreateContractDTO;
use App\Domain\Admission\Services\ContractService;
use App\Models\EnrollmentContract;

class GenerateContract
{
    public function __construct(protected ContractService $service) {}

    public function execute(CreateContractDTO $dto, ?int $userId = null): EnrollmentContract
    {
        return $this->service->generateContract($dto, $userId);
    }
}
