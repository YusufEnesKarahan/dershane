<?php

namespace App\Domain\HR\Services;

use App\Core\Repositories\Interfaces\ExpenseRepositoryInterface;
use App\DTOs\HR\AdvanceDTO;

class AdvanceService
{
    public function __construct(
        protected ExpenseRepositoryInterface $expenseRepo
    ) {}

    public function allAdvances()
    {
        return $this->expenseRepo->allAdvances();
    }

    public function findAdvance(int $id)
    {
        return $this->expenseRepo->findAdvance($id);
    }

    public function createAdvance(AdvanceDTO $dto)
    {
        return $this->expenseRepo->createAdvance([
            'employee_id' => $dto->employeeId,
            'amount' => $dto->amount,
            'reason' => $dto->reason,
            'status' => 'Pending'
        ]);
    }

    public function approveAdvance(int $id)
    {
        return $this->expenseRepo->updateAdvance($id, [
            'status' => 'Approved'
        ]);
    }

    public function rejectAdvance(int $id)
    {
        return $this->expenseRepo->updateAdvance($id, [
            'status' => 'Rejected'
        ]);
    }
}
