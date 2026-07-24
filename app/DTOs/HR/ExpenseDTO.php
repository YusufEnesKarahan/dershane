<?php

namespace App\DTOs\HR;

class ExpenseDTO
{
    public function __construct(
        public int $employeeId,
        public string $title,
        public float $amount,
        public string $category,
        public ?string $receipt
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            employeeId: (int) $request->input('employee_id'),
            title: $request->input('title'),
            amount: (float) $request->input('amount'),
            category: $request->input('category'),
            receipt: $request->input('receipt')
        );
    }
}
