<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\DTOs\Student\StudentEnrollmentDTO;
use App\Domain\Student\Actions\EnrollStudentAction;
use Illuminate\Http\Request;

class StudentEnrollmentController extends Controller
{
    public function store(Request $request, EnrollStudentAction $action)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $dto = StudentEnrollmentDTO::fromRequest($request->all());

        try {
            $action->execute($dto);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->back()->with('success', 'Kursa başarıyla kaydolundu.');
    }
}
