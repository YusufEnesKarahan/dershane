<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\AssignmentSubmission;
use Illuminate\Support\Collection;

interface AssignmentSubmissionRepositoryInterface
{
    public function getByAssignment(int $assignmentId): Collection;
    public function createOrUpdate(array $data): AssignmentSubmission;
}
