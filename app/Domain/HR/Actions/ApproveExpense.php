<?php

namespace App\Domain\HR\Actions;

use App\Domain\HR\Services\ExpenseService;

class ApproveExpense
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    public function execute(int $id)
    {
        return $this->expenseService->approveExpense($id);
    }
}
