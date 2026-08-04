<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student ?? \App\Models\Student::first();
        $exams = Exam::where('status', 'published')
            ->orderBy('exam_date', 'desc')
            ->paginate(15);
            
        $studentId = $student?->id ?? 1;
        $results = ExamResult::where('student_id', $studentId)->get()->keyBy('exam_id');
        
        return view('student.exams.index', compact('exams', 'results'));
    }

    public function showResult(Exam $exam)
    {
        $student = Auth::user()->student;
        
        $result = ExamResult::with('answers.course')
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->firstOrFail();
            
        $this->authorize('view', $result);
            
        return view('student.exams.show', compact('exam', 'result'));
    }
}
