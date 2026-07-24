<?php

namespace App\Domain\HR\Services;

use App\Core\Repositories\Interfaces\LeaveRepositoryInterface;
use App\DTOs\HR\LeaveRequestDTO;
use Carbon\Carbon;

class LeaveService
{
    public function __construct(
        protected LeaveRepositoryInterface $leaveRepo
    ) {}

    public function allRequests()
    {
        return $this->leaveRepo->allRequests();
    }

    public function allTypes()
    {
        return $this->leaveRepo->allTypes();
    }

    public function findRequest(int $id)
    {
        return $this->leaveRepo->findRequest($id);
    }

    public function createRequest(LeaveRequestDTO $dto)
    {
        $start = Carbon::parse($dto->startDate);
        $end = Carbon::parse($dto->endDate);
        $days = $start->diffInDays($end) + 1;

        return $this->leaveRepo->createRequest([
            'employee_id' => $dto->employeeId,
            'leave_type_id' => $dto->leaveTypeId,
            'start_date' => $dto->startDate,
            'end_date' => $dto->endDate,
            'days' => $days,
            'reason' => $dto->reason,
            'status' => 'Pending'
        ]);
    }

    public function approveRequest(int $id, int $userId)
    {
        return $this->leaveRepo->updateRequest($id, [
            'status' => 'Approved',
            'approved_by' => $userId,
            'approved_at' => now()
        ]);
    }

    public function rejectRequest(int $id, int $userId)
    {
        return $this->leaveRepo->updateRequest($id, [
            'status' => 'Rejected',
            'approved_by' => $userId,
            'approved_at' => now()
        ]);
    }
}
