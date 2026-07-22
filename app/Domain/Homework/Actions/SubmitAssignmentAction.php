<?php
namespace App\Domain\Homework\Actions;

use App\DTOs\Homework\SubmitAssignmentDTO;
use App\Domain\Homework\Services\SubmissionService;
use App\Models\AssignmentSubmission;

class SubmitAssignmentAction
{
    public function __construct(protected SubmissionService $service) {}

    public function execute(SubmitAssignmentDTO $dto): AssignmentSubmission
    {
        return $this->service->submit($dto);
    }
}
