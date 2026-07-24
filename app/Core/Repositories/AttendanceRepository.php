<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\AttendanceRepositoryInterface;
use App\Models\EmployeeAttendance;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function all()
    {
        return EmployeeAttendance::with('employee.department')->orderBy('date', 'desc')->get();
    }

    public function find(int $id)
    {
        return EmployeeAttendance::with('employee')->findOrFail($id);
    }

    public function create(array $data)
    {
        return EmployeeAttendance::create($data);
    }

    public function update(int $id, array $data)
    {
        $attendance = EmployeeAttendance::findOrFail($id);
        $attendance->update($data);
        return $attendance;
    }

    public function getForEmployee(int $employeeId, ?string $startDate = null, ?string $endDate = null)
    {
        $query = EmployeeAttendance::where('employee_id', $employeeId);
        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }
        return $query->orderBy('date', 'asc')->get();
    }
}
