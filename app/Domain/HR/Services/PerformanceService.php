<?php

namespace App\Domain\HR\Services;

use App\Core\Repositories\Interfaces\PerformanceRepositoryInterface;
use App\DTOs\HR\PerformanceReviewDTO;

class PerformanceService
{
    public function __construct(
        protected PerformanceRepositoryInterface $performanceRepo
    ) {}

    public function allReviews()
    {
        return $this->performanceRepo->allReviews();
    }

    public function findReview(int $id)
    {
        return $this->performanceRepo->findReview($id);
    }

    public function createReview(PerformanceReviewDTO $dto)
    {
        return $this->performanceRepo->createReview([
            'employee_id' => $dto->employeeId,
            'reviewer_id' => $dto->reviewerId,
            'period' => $dto->period,
            'score' => $dto->score,
            'strengths' => $dto->strengths,
            'weaknesses' => $dto->weaknesses,
            'comments' => $dto->comments
        ]);
    }
}
