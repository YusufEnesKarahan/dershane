<?php

namespace App\DTOs\HR;

class AdvanceDTO
{
    public function __construct(
        public int $employeeId,
        public float $amount,
        public string $reason
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            employeeId: (int) $request->input('employee_id'),
            amount: (float) $request->input('amount'),
            reason: $request->input('reason', '')
        );
    }
}
