<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Domain\Teacher\Services\TeacherSalaryService;
use Illuminate\Http\Request;

class TeacherSalaryController extends Controller
{
    public function __construct(protected TeacherSalaryService $service) {}

    public function index()
    {
        $this->authorize('viewAny', Teacher::class);

        $teachers = Teacher::with(['user', 'salaryProfile'])->get();

        return view('admin.teachers.salary', compact('teachers'));
    }

    public function store(Request $request)
    {
        $this->authorize('update', Teacher::class);

        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'base_salary' => 'required|numeric',
        ]);

        $teacher = Teacher::findOrFail($request->teacher_id);
        $this->service->saveSalary($teacher, $request->all());

        return redirect()->back()->with('success', 'Salary configuration saved successfully.');
    }
}
