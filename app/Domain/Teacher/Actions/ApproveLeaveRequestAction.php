<?php
namespace App\Domain\Teacher\Actions;

use App\Models\TeacherLeaveRequest;

class ApproveLeaveRequestAction
{
    public function execute(TeacherLeaveRequest $request): void
    {
        $request->update(['status' => 'Approved']);
    }
}
