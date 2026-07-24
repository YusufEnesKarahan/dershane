<?php

namespace App\Core\Repositories\Interfaces;

interface ExpenseRepositoryInterface
{
    public function allExpenses();
    public function findExpense(int $id);
    public function createExpense(array $data);
    public function updateExpense(int $id, array $data);

    public function allAdvances();
    public function findAdvance(int $id);
    public function createAdvance(array $data);
    public function updateAdvance(int $id, array $data);
}
