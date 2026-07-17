<?php
namespace App\DTOs\Teacher;

class TeacherSalaryDTO
{
    public function __construct(
        public float $base_salary,
        public string $payment_type = 'Monthly',
        public float $bonus = 0.0,
        public float $deductions = 0.0
    ) {}
}
