<?php
namespace App\Core\Repositories;

use App\Models\AssignmentSubmission;
use App\Core\Repositories\Interfaces\AssignmentSubmissionRepositoryInterface;
use Illuminate\Support\Collection;

class AssignmentSubmissionRepository implements AssignmentSubmissionRepositoryInterface
{
    public function getByAssignment(int $assignmentId): Collection
    {
        return AssignmentSubmission::with(['student.branch', 'score', 'files', 'comments.user'])
            ->where('assignment_id', $assignmentId)
            ->orderBy('submission_date', 'desc')
            ->get();
    }

    public function createOrUpdate(array $data): AssignmentSubmission
    {
        return AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $data['assignment_id'],
                'student_id' => $data['student_id'],
            ],
            $data
        );
    }
}
