<?php
namespace App\Domain\Attendance\Services;

use App\DTOs\Attendance\CreateAttendanceSessionDTO;
use App\Core\Repositories\Interfaces\AttendanceSessionRepositoryInterface;
use App\Models\AttendanceSession;
use App\Models\Student;
use Illuminate\Support\Collection;

class AttendanceSessionService
{
    public function __construct(protected AttendanceSessionRepositoryInterface $repository) {}

    public function createSession(CreateAttendanceSessionDTO $dto): AttendanceSession
    {
        return $this->repository->create([
            'classroom_id' => $dto->classroom_id,
            'course_id' => $dto->course_id,
            'teacher_id' => $dto->teacher_id,
            'session_date' => $dto->session_date,
            'start_time' => $dto->start_time,
            'end_time' => $dto->end_time,
            'class_schedule_id' => $dto->class_schedule_id,
            'status' => $dto->status,
        ]);
    }

    public function getEligibleStudents(AttendanceSession $session): Collection
    {
        // Enrolled students for this course or classroom
        return Student::where('classroom_id', $session->classroom_id)
            ->orWhereHas('enrollments', function ($q) use ($session) {
                $q->where('course_id', $session->course_id);
            })->get();
    }
}
