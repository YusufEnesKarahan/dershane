<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\TeacherSchedule;
use App\DTOs\Teacher\TeacherScheduleDTO;
use App\Domain\Teacher\Services\TeacherScheduleService;
use App\Core\Repositories\Interfaces\TeacherScheduleRepositoryInterface;
use Illuminate\Http\Request;

class TeacherScheduleController extends Controller
{
    public function __construct(
        protected TeacherScheduleService $service,
        protected TeacherScheduleRepositoryInterface $repository
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Teacher::class);

        $teacherId = $request->query('teacher_id');
        $teachers = Teacher::with('user')->get();
        
        $schedules = collect();
        $teacher = null;
        $classrooms = Classroom::all();
        $courses = Course::all();

        if ($teacherId) {
            $teacher = Teacher::with('user')->findOrFail($teacherId);
            $schedules = $this->repository->getByTeacher($teacherId);
        }

        return view('admin.teachers.schedules', compact('teachers', 'teacher', 'schedules', 'classrooms', 'courses'));
    }

    public function store(Request $request)
    {
        $this->authorize('update', Teacher::class);

        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'course_id' => 'nullable|exists:courses,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $dto = TeacherScheduleDTO::fromRequest($request->all());

        try {
            $this->service->createSchedule($dto);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->back()->with('success', 'Schedule created successfully without overlapping conflicts.');
    }
}
