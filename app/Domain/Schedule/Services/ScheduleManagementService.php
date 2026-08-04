<?php

namespace App\Domain\Schedule\Services;

use App\Models\LessonSchedule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use ValidationException;

class ScheduleManagementService
{
    public function createSchedule(array $data): LessonSchedule
    {
        $this->checkConflicts($data);

        return DB::transaction(function () use ($data) {
            return LessonSchedule::create($data);
        });
    }

    public function updateSchedule(LessonSchedule $schedule, array $data): LessonSchedule
    {
        $this->checkConflicts($data, $schedule->id);

        return DB::transaction(function () use ($schedule, $data) {
            $schedule->update($data);
            return $schedule->fresh();
        });
    }

    public function deleteSchedule(LessonSchedule $schedule): bool
    {
        return DB::transaction(function () use ($schedule) {
            return $schedule->delete();
        });
    }

    public function checkConflicts(array $data, ?int $ignoreId = null): void
    {
        $day = $data['day_of_week'];
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];

        // 1. Teacher conflict check
        if (!empty($data['teacher_id'])) {
            $teacherQuery = LessonSchedule::where('day_of_week', $day)
                ->where('teacher_id', $data['teacher_id'])
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime);

            if ($ignoreId) {
                $teacherQuery->where('id', '!=', $ignoreId);
            }

            if ($teacherQuery->exists()) {
                throw new \InvalidArgumentException('Öğretmenin bu saat diliminde başka bir dersi bulunmaktadır.');
            }
        }

        // 2. Classroom conflict check
        if (!empty($data['classroom_id'])) {
            $classroomQuery = LessonSchedule::where('day_of_week', $day)
                ->where('classroom_id', $data['classroom_id'])
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime);

            if ($ignoreId) {
                $classroomQuery->where('id', '!=', $ignoreId);
            }

            if ($classroomQuery->exists()) {
                throw new \InvalidArgumentException('Sınıfın bu saat diliminde başka bir dersi bulunmaktadır.');
            }
        }
    }

    public function getClassroomSchedules(int $classroomId)
    {
        return LessonSchedule::with(['course', 'teacher', 'lessonPeriod'])
            ->where('classroom_id', $classroomId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    public function getTeacherSchedules(int $teacherId)
    {
        return LessonSchedule::with(['classroom', 'course', 'lessonPeriod'])
            ->where('teacher_id', $teacherId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    public function getStudentSchedules(Student $student)
    {
        if (!$student->classroom_id) {
            return collect();
        }

        return $this->getClassroomSchedules($student->classroom_id);
    }

    public function getParentStudentSchedules(User $parentUser)
    {
        $guardian = \App\Models\StudentGuardian::where('user_id', $parentUser->id)->first();
        if (!$guardian) {
            return collect();
        }

        $student = $guardian->student;
        if (!$student) {
            return collect();
        }

        return $this->getStudentSchedules($student);
    }
}
