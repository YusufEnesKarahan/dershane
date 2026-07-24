<?php

namespace App\Domain\HR\Services;

use App\Core\Repositories\Interfaces\ExpenseRepositoryInterface;
use App\DTOs\HR\ExpenseDTO;

class ExpenseService
{
    public function __construct(
        protected ExpenseRepositoryInterface $expenseRepo
    ) {}

    public function allExpenses()
    {
        return $this->expenseRepo->allExpenses();
    }

    public function findExpense(int $id)
    {
        return $this->expenseRepo->findExpense($id);
    }

    public function createExpense(ExpenseDTO $dto)
    {
        return $this->expenseRepo->createExpense([
            'employee_id' => $dto->employeeId,
            'title' => $dto->title,
            'amount' => $dto->amount,
            'category' => $dto->category,
            'receipt' => $dto->receipt,
            'status' => 'Pending'
        ]);
    }

    public function approveExpense(int $id)
    {
        return $this->expenseRepo->updateExpense($id, [
            'status' => 'Approved'
        ]);
    }

    public function rejectExpense(int $id)
    {
        return $this->expenseRepo->updateExpense($id, [
            'status' => 'Rejected'
        ]);
    }
}
