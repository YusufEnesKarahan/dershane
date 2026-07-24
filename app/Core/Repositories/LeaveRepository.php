<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\LeaveRepositoryInterface;
use App\Models\LeaveType;
use App\Models\LeaveRequest;

class LeaveRepository implements LeaveRepositoryInterface
{
    public function allTypes()
    {
        return LeaveType::all();
    }

    public function allRequests()
    {
        return LeaveRequest::with(['employee.department', 'leaveType', 'approver'])->get();
    }

    public function findRequest(int $id)
    {
        return LeaveRequest::with(['employee', 'leaveType'])->findOrFail($id);
    }

    public function createRequest(array $data)
    {
        return LeaveRequest::create($data);
    }

    public function updateRequest(int $id, array $data)
    {
        $req = LeaveRequest::findOrFail($id);
        $req->update($data);
        return $req;
    }

    public function getRequestsForEmployee(int $employeeId)
    {
        return LeaveRequest::with('leaveType')->where('employee_id', $employeeId)->get();
    }
}
