<?php

namespace App\Domain\HR\Actions;

use App\Domain\HR\Services\PayrollService;
use App\DTOs\HR\CreatePayrollDTO;

class GeneratePayroll
{
    public function __construct(
        protected PayrollService $payrollService
    ) {}

    public function execute(CreatePayrollDTO $dto)
    {
        return $this->payrollService->generatePayroll($dto);
    }
}
