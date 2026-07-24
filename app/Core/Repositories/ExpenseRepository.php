<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\ExpenseRepositoryInterface;
use App\Models\Expense;
use App\Models\Advance;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function allExpenses()
    {
        return Expense::with('employee.department')->orderBy('created_at', 'desc')->get();
    }

    public function findExpense(int $id)
    {
        return Expense::with('employee')->findOrFail($id);
    }

    public function createExpense(array $data)
    {
        return Expense::create($data);
    }

    public function updateExpense(int $id, array $data)
    {
        $exp = Expense::findOrFail($id);
        $exp->update($data);
        return $exp;
    }

    public function allAdvances()
    {
        return Advance::with('employee.department')->orderBy('created_at', 'desc')->get();
    }

    public function findAdvance(int $id)
    {
        return Advance::with('employee')->findOrFail($id);
    }

    public function createAdvance(array $data)
    {
        return Advance::create($data);
    }

    public function updateAdvance(int $id, array $data)
    {
        $adv = Advance::findOrFail($id);
        $adv->update($data);
        return $adv;
    }
}
