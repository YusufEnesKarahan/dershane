<?php

namespace App\Domain\Dashboard\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\LessonSchedule;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Homework;
use App\Models\Notification;
use App\Domain\Notification\Services\NotificationService;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function getAdminDashboardData(?int $branchId = null): array
    {
        $branchId = $branchId ?? auth()->user()?->branch_id;
        $todayName = Carbon::now()->format('l');

        $totalStudents = Student::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        $totalTeachers = Teacher::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        $activeClassrooms = Classroom::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();

        $todaysLessons = LessonSchedule::with(['classroom', 'teacher', 'course'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('day_of_week', $todayName)
            ->get();

        $todaysAttendanceCount = AttendanceRecord::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('attendance_date', Carbon::today()->toDateString())
            ->count();

        $recentExams = Exam::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->take(5)
            ->get();

        $recentHomeworks = Homework::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->take(5)
            ->get();

        $recentNotifications = Notification::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->take(5)
            ->get();

        return compact(
            'totalStudents',
            'totalTeachers',
            'activeClassrooms',
            'todaysLessons',
            'todaysAttendanceCount',
            'recentExams',
            'recentHomeworks',
            'recentNotifications'
        );
    }

    public function getTeacherDashboardData(User $user): array
    {
        $teacher = $user->teacher;
        $todayName = Carbon::now()->format('l');

        if (!$teacher) {
            return [
                'todaysLessons' => collect(),
                'pendingHomeworkChecks' => collect(),
                'studentPerformanceSummary' => [],
                'notifications' => collect(),
            ];
        }

        $todaysLessons = LessonSchedule::with(['classroom', 'course'])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $todayName)
            ->get();

        $pendingHomeworkChecks = Homework::with('classroom')
            ->where('teacher_id', $teacher->id)
            ->where('status', 'published')
            ->latest()
            ->take(5)
            ->get();

        $notifications = $this->notificationService->getUserNotifications($user, 5);

        return compact(
            'todaysLessons',
            'pendingHomeworkChecks',
            'notifications'
        );
    }

    public function getStudentDashboardData(User $user): array
    {
        $student = $user->student;
        $todayName = Carbon::now()->format('l');

        if (!$student) {
            return [
                'upcomingExams' => collect(),
                'activeHomeworks' => collect(),
                'schedule' => collect(),
                'notifications' => collect(),
            ];
        }

        $upcomingExams = Exam::where('branch_id', $student->branch_id)
            ->where('exam_date', '>=', Carbon::today()->toDateString())
            ->orderBy('exam_date', 'asc')
            ->take(5)
            ->get();

        $activeHomeworks = Homework::where('classroom_id', $student->classroom_id)
            ->where('due_date', '>=', Carbon::now())
            ->orderBy('due_date', 'asc')
            ->get();

        $schedule = LessonSchedule::with(['course', 'teacher'])
            ->where('classroom_id', $student->classroom_id)
            ->where('day_of_week', $todayName)
            ->get();

        $notifications = $this->notificationService->getUserNotifications($user, 5);

        return compact(
            'upcomingExams',
            'activeHomeworks',
            'schedule',
            'notifications'
        );
    }

    public function getParentDashboardData(User $user): array
    {
        $guardian = $user->guardian;

        if (!$guardian) {
            return [
                'child' => null,
                'attendanceSummary' => [],
                'recentExamResults' => collect(),
                'activeHomeworks' => collect(),
                'notifications' => collect(),
            ];
        }

        $child = $guardian->students()->first();

        if (!$child) {
            return [
                'child' => null,
                'attendanceSummary' => [],
                'recentExamResults' => collect(),
                'activeHomeworks' => collect(),
                'notifications' => collect(),
            ];
        }

        $attendanceSummary = [
            'present' => AttendanceRecord::where('student_id', $child->id)->where('status', 'present')->count(),
            'absent' => AttendanceRecord::where('student_id', $child->id)->where('status', 'absent')->count(),
            'late' => AttendanceRecord::where('student_id', $child->id)->where('status', 'late')->count(),
        ];

        $recentExamResults = ExamResult::with('exam')
            ->where('student_id', $child->id)
            ->latest()
            ->take(5)
            ->get();

        $activeHomeworks = Homework::where('classroom_id', $child->classroom_id)
            ->where('due_date', '>=', Carbon::now())
            ->take(5)
            ->get();

        $notifications = $this->notificationService->getUserNotifications($user, 5);

        return compact(
            'child',
            'attendanceSummary',
            'recentExamResults',
            'activeHomeworks',
            'notifications'
        );
    }
}
