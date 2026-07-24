<?php

namespace App\Domain\HR\Actions;

use App\Domain\HR\Services\PerformanceService;
use App\DTOs\HR\PerformanceReviewDTO;

class EvaluateEmployee
{
    public function __construct(
        protected PerformanceService $performanceService
    ) {}

    public function execute(PerformanceReviewDTO $dto)
    {
        return $this->performanceService->createReview($dto);
    }
}
