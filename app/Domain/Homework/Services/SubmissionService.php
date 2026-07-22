<?php
namespace App\Domain\Homework\Services;

use App\DTOs\Homework\SubmitAssignmentDTO;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentFile;
use Illuminate\Validation\ValidationException;

class SubmissionService
{
    public function submit(SubmitAssignmentDTO $dto): AssignmentSubmission
    {
        $assignment = Assignment::findOrFail($dto->assignment_id);

        $exists = AssignmentSubmission::where('assignment_id', $dto->assignment_id)
            ->where('student_id', $dto->student_id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'student_id' => 'Bu ödev için daha önce teslim yapılmıştır.',
            ]);
        }

        $now = now();
        $dueDate = \Carbon\Carbon::parse($assignment->due_date);
        $isLate = $now->greaterThan($dueDate);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $dto->assignment_id,
            'student_id' => $dto->student_id,
            'submission_date' => $now,
            'is_late' => $isLate,
            'remarks' => $dto->remarks,
            'status' => 'Pending',
        ]);

        if (!empty($dto->files)) {
            foreach ($dto->files as $file) {
                AssignmentFile::create([
                    'submission_id' => $submission->id,
                    'title' => $file['title'] ?? 'Ödev Dosyası',
                    'file_path' => $file['file_path'],
                    'file_type' => $file['file_type'] ?? null,
                    'size_bytes' => $file['size_bytes'] ?? null,
                ]);
            }
        }

        return $submission;
    }
}
