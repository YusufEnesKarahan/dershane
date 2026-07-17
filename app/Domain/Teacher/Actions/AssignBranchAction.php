<?php
namespace App\Domain\Teacher\Actions;

use App\Models\Teacher;

class AssignBranchAction
{
    public function execute(Teacher $teacher, int $branchId): void
    {
        $teacher->update(['branch_id' => $branchId]);
    }
}
