<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Student;
use App\Models\ExamResult;
use App\Domain\Exam\Services\ExamResultService;
use Illuminate\Http\Request;

class TeacherExamController extends Controller
{
    public function __construct(
        protected ExamResultService $resultService
    ) {}

    public function index()
    {
        $exams = Exam::with('subjects.course')->latest()->paginate(15);
        return view('teacher.exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        $exam->load('subjects.course', 'results.student');
        return view('teacher.exams.show', compact('exam'));
    }

    public function results(Exam $exam)
    {
        $students = Student::all(); // Alternatively, students in teacher's classrooms
        $exam->load('subjects.course', 'results.answers');
        
        return view('teacher.exams.results', compact('exam', 'students'));
    }

    public function storeResult(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'score' => 'required|numeric|min:0',
            'correct_answers' => 'nullable|integer|min:0',
            'wrong_answers' => 'nullable|integer|min:0',
            'empty_answers' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'answers' => 'nullable|array',
            'answers.*.correct' => 'nullable|integer|min:0',
            'answers.*.wrong' => 'nullable|integer|min:0',
            'answers.*.empty' => 'nullable|integer|min:0',
        ]);

        $this->resultService->submitResult($exam, $validated);

        return redirect()->back()->with('success', 'Öğrenci sınav sonucu kaydedildi.');
    }
}
