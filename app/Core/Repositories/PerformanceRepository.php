<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\PerformanceRepositoryInterface;
use App\Models\PerformanceReview;

class PerformanceRepository implements PerformanceRepositoryInterface
{
    public function allReviews()
    {
        return PerformanceReview::with(['employee.department', 'reviewer'])->orderBy('created_at', 'desc')->get();
    }

    public function findReview(int $id)
    {
        return PerformanceReview::with(['employee', 'reviewer'])->findOrFail($id);
    }

    public function createReview(array $data)
    {
        return PerformanceReview::create($data);
    }

    public function updateReview(int $id, array $data)
    {
        $rev = PerformanceReview::findOrFail($id);
        $rev->update($data);
        return $rev;
    }

    public function getForEmployee(int $employeeId)
    {
        return PerformanceReview::with('reviewer')->where('employee_id', $employeeId)->orderBy('created_at', 'desc')->get();
    }
}
