<?php

namespace App\DTOs\HR;

class LeaveRequestDTO
{
    public function __construct(
        public int $employeeId,
        public int $leaveTypeId,
        public string $startDate,
        public string $endDate,
        public string $reason
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            employeeId: (int) $request->input('employee_id'),
            leaveTypeId: (int) $request->input('leave_type_id'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            reason: $request->input('reason', '')
        );
    }
}
