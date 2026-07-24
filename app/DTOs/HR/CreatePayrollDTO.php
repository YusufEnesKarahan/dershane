<?php

namespace App\DTOs\HR;

class CreatePayrollDTO
{
    public function __construct(
        public int $employeeId,
        public int $month,
        public int $year,
        public float $bonus,
        public float $overtimeAmount,
        public float $deductions
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            employeeId: (int) $request->input('employee_id'),
            month: (int) $request->input('month', date('n')),
            year: (int) $request->input('year', date('Y')),
            bonus: (float) $request->input('bonus', 0.0),
            overtimeAmount: (float) $request->input('overtime_amount', 0.0),
            deductions: (float) $request->input('deductions', 0.0)
        );
    }
}
