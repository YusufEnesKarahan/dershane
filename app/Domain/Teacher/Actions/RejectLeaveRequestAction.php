<?php
namespace App\Domain\Teacher\Actions;

use App\Models\TeacherLeaveRequest;

class RejectLeaveRequestAction
{
    public function execute(TeacherLeaveRequest $request): void
    {
        $request->update(['status' => 'Rejected']);
    }
}
