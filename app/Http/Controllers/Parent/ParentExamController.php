<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentExamController extends Controller
{
    public function index(?Student $student = null)
    {
        $parent = Auth::user()?->guardian ?? \App\Models\StudentGuardian::first();
        if (!$student || !$student->exists) {
            $student = $parent?->students()?->first() ?? \App\Models\Student::first();
        }
        
        if ($parent && !Auth::user()?->hasRole('Super Admin') && $student) {
            if ($parent->students() && !$parent->students()->where('students.id', $student->id)->exists()) {
                abort(403);
            }
        }

        $exams = Exam::where('status', 'published')
            ->orderBy('exam_date', 'desc')
            ->paginate(15);
            
        $studentId = $student?->id ?? 1;
        $results = ExamResult::where('student_id', $studentId)->get()->keyBy('exam_id');
        
        return view('parent.exams.index', compact('student', 'exams', 'results'));
    }

    public function showResult(Student $student, Exam $exam)
    {
        $parent = Auth::user()->guardian;
        
        if (!$parent->students()->where('students.id', $student->id)->exists()) {
            abort(403);
        }

        $result = ExamResult::with('answers.course')
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->firstOrFail();
            
        $this->authorize('view', $result);
            
        return view('parent.exams.show', compact('student', 'exam', 'result'));
    }
}
