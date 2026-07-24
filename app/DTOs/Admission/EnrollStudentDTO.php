<?php

namespace App\DTOs\Admission;

class EnrollStudentDTO
{
    public function __construct(
        public int $studentAdmissionId,
        public ?int $classroomId = null,
        public float $finalFee = 0.00,
        public string $academicYear = '2026-2027',
        public int $installmentCount = 1
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['student_admission_id'],
            isset($data['classroom_id']) ? (int) $data['classroom_id'] : null,
            isset($data['final_fee']) ? (float) $data['final_fee'] : 0.00,
            $data['academic_year'] ?? '2026-2027',
            isset($data['installment_count']) ? (int) $data['installment_count'] : 1
        );
    }
}
