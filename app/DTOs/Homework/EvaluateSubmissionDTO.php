<?php
namespace App\DTOs\Homework;

class EvaluateSubmissionDTO
{
    public function __construct(
        public int $submission_id,
        public float $score,
        public ?int $evaluator_id = null,
        public ?string $feedback = null,
        public float $max_score = 100.0
    ) {}
}
