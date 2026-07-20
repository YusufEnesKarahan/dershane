<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\ClassSchedule;
use App\DTOs\Classroom\ClassScheduleDTO;
use App\Domain\Classroom\Actions\CreateScheduleAction;
use Illuminate\Http\Request;

class ClassScheduleController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::all();
        $teachers = Teacher::with('user')->get();
        $courses = Course::all();

        $selectedClassroomId = $request->query('classroom_id', $classrooms->first()?->id);
        $schedules = $selectedClassroomId ? ClassSchedule::with(['teacher.user', 'course'])->where('classroom_id', $selectedClassroomId)->get() : collect();

        return view('admin.classrooms.schedules', compact('classrooms', 'teachers', 'courses', 'selectedClassroomId', 'schedules'));
    }

    public function store(Request $request, CreateScheduleAction $action)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id' => 'required|exists:teachers,id',
            'course_id' => 'required|exists:courses,id',
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $dto = ClassScheduleDTO::fromRequest($request->all());

        try {
            $action->execute($dto);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->back()->with('success', 'Ders programı başarıyla kaydedildi.');
    }
}
