<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Domain\Teacher\Services\TeacherPerformanceService;
use App\Core\Repositories\Interfaces\TeacherPerformanceRepositoryInterface;
use Illuminate\Http\Request;

class TeacherPerformanceController extends Controller
{
    public function __construct(
        protected TeacherPerformanceService $service,
        protected TeacherPerformanceRepositoryInterface $repository
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Teacher::class);

        $teachers = Teacher::with('user')->get();
        $teacherId = $request->query('teacher_id');
        $performances = collect();
        $teacher = null;

        if ($teacherId) {
            $teacher = Teacher::with('user')->findOrFail($teacherId);
            $performances = $this->repository->getByTeacher($teacherId);
        }

        return view('admin.teachers.performance', compact('teachers', 'teacher', 'performances'));
    }

    public function store(Request $request)
    {
        $this->authorize('update', Teacher::class);

        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'attendance_rate' => 'required|numeric',
            'student_satisfaction' => 'required|numeric',
        ]);

        $teacher = Teacher::findOrFail($request->teacher_id);
        $this->service->logPerformance($teacher, $request->all());

        return redirect()->back()->with('success', 'KPI metrics logged successfully.');
    }
}
