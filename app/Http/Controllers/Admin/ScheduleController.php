<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonSchedule;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\AcademicTerm;
use App\Models\LessonPeriod;
use App\Domain\Schedule\Services\ScheduleManagementService;
use App\Domain\Schedule\Services\LessonPeriodService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ScheduleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ScheduleManagementService $scheduleService,
        protected LessonPeriodService $periodService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', LessonSchedule::class);

        $classroomId = $request->get('classroom_id');
        $teacherId = $request->get('teacher_id');

        $schedules = collect();
        if ($classroomId) {
            $schedules = $this->scheduleService->getClassroomSchedules($classroomId);
        } elseif ($teacherId) {
            $schedules = $this->scheduleService->getTeacherSchedules($teacherId);
        } else {
            $schedules = LessonSchedule::with(['classroom', 'teacher', 'course', 'lessonPeriod'])
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();
        }

        $classrooms = Classroom::all();
        $teachers = Teacher::all();

        return view('admin.schedule.index', compact('schedules', 'classrooms', 'teachers', 'classroomId', 'teacherId'));
    }

    public function create()
    {
        $this->authorize('create', LessonSchedule::class);

        $classrooms = Classroom::all();
        $teachers = Teacher::all();
        $courses = Course::all();
        $academicTerms = AcademicTerm::all();
        $periods = $this->periodService->getAllPeriods();

        return view('admin.schedule.create', compact('classrooms', 'teachers', 'courses', 'academicTerms', 'periods'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', LessonSchedule::class);

        $validated = $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'lesson_period_id' => 'nullable|exists:lesson_periods,id',
            'day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room' => 'nullable|string|max:100',
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;

        try {
            $this->scheduleService->createSchedule($validated);
            return redirect()->route('admin.schedule.index')->with('success', 'Ders programı başarıyla oluşturuldu.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ders programı eklenirken bir hata oluştu: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(LessonSchedule $schedule)
    {
        $this->authorize('update', $schedule);

        $classrooms = Classroom::all();
        $teachers = Teacher::all();
        $courses = Course::all();
        $academicTerms = AcademicTerm::all();
        $periods = $this->periodService->getAllPeriods();

        return view('admin.schedule.edit', compact('schedule', 'classrooms', 'teachers', 'courses', 'academicTerms', 'periods'));
    }

    public function update(Request $request, LessonSchedule $schedule)
    {
        $this->authorize('update', $schedule);

        $validated = $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'lesson_period_id' => 'nullable|exists:lesson_periods,id',
            'day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room' => 'nullable|string|max:100',
        ]);

        try {
            $this->scheduleService->updateSchedule($schedule, $validated);
            return redirect()->route('admin.schedule.index')->with('success', 'Ders programı güncellendi.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ders programı güncellenirken bir hata oluştu: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(LessonSchedule $schedule)
    {
        $this->authorize('delete', $schedule);

        $this->scheduleService->deleteSchedule($schedule);

        return redirect()->route('admin.schedule.index')->with('success', 'Ders programı silindi.');
    }
}
