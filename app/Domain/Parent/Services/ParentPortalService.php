<?php

namespace App\Domain\Parent\Services;

use App\DTOs\Parent\ParentDashboardDTO;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\Assignment;
use App\Models\Invoice;
use App\Models\Announcement;
use App\Core\Repositories\Interfaces\ParentRepositoryInterface;
use Illuminate\Support\Collection;

class ParentPortalService
{
    public function __construct(protected ParentRepositoryInterface $repository) {}

    public function getLinkedStudents(int $parentId): Collection
    {
        return $this->repository->getLinkedStudents($parentId);
    }

    public function canAccessStudent(int $parentId, int $studentId): bool
    {
        $user = \App\Models\User::find($parentId);
        if ($user && ($user->roles->pluck('name')->contains('Administrator') || $user->hasRole('Administrator'))) {
            return true;
        }
        return $this->repository->isStudentLinked($parentId, $studentId);
    }

    public function getDashboardData(int $parentId, int $studentId): ParentDashboardDTO
    {
        abort_unless($this->canAccessStudent($parentId, $studentId), 404);

        $student = Student::with(['classroom', 'branch'])->findOrFail($studentId);

        // Fetch Attendance
        $attendance = Attendance::where('student_id', $studentId)
            ->with('session')
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch Exam Results
        $examResults = ExamResult::where('student_id', $studentId)
            ->with('exam')
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch Homework Assignments targeted to this student's class
        $classroomId = $student->classroom_id;
        $homeworks = Assignment::where('classroom_id', $classroomId)
            ->with(['submissions' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            }])
            ->orderBy('due_date', 'asc')
            ->get();

        // Fetch Invoices
        $invoices = Invoice::where('student_id', $studentId)
            ->with('payments')
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch Announcements targeted to general or group
        $announcements = Announcement::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->get();

        return new ParentDashboardDTO(
            $student,
            $attendance,
            $examResults,
            $homeworks,
            $invoices,
            $announcements
        );
    }
}
