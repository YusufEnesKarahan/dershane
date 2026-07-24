<?php

namespace App\DTOs\HR;

class PerformanceReviewDTO
{
    public function __construct(
        public int $employeeId,
        public int $reviewerId,
        public string $period,
        public int $score,
        public ?string $strengths,
        public ?string $weaknesses,
        public ?string $comments
    ) {}

    public static function fromRequest($request, int $reviewerId): self
    {
        return new self(
            employeeId: (int) $request->input('employee_id'),
            reviewerId: $reviewerId,
            period: $request->input('period'),
            score: (int) $request->input('score'),
            strengths: $request->input('strengths'),
            weaknesses: $request->input('weaknesses'),
            comments: $request->input('comments')
        );
    }
}
