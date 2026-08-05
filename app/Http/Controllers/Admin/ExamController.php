<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Course;
use App\Models\Student;
use App\Domain\Exam\Services\ExamManagementService;
use App\Domain\Exam\Services\ExamAnalysisService;
use App\Domain\Academic\Services\AcademicProfessionalService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(
        protected ExamManagementService $service,
        protected ExamAnalysisService $analysisService,
        protected AcademicProfessionalService $academicService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Exam::class);
        $exams = Exam::latest()->paginate(15);
        $totalExams = Exam::count();
        $upcomingExams = Exam::where('exam_date', '>=', now())->count();
        
        return view('admin.exams.index', compact('exams', 'totalExams', 'upcomingExams'));
    }

    public function create()
    {
        $this->authorize('create', Exam::class);
        $courses = Course::all();
        
        return view('admin.exams.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Exam::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:TYT,AYT,LGS,YKS,Kurumsal Deneme,mock_exam,practice_exam,final_exam,quiz',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
            'exam_date' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_score' => 'required|numeric|min:1',
            'subjects' => 'nullable|array',
            'subjects.*.course_id' => 'required|exists:courses,id',
            'subjects.*.question_count' => 'required|integer|min:1',
            'subjects.*.max_score' => 'required|numeric|min:1',
        ]);

        try {
            $exam = $this->service->createExam($validated);
            return redirect()->route('admin.exams.index')->with('success', 'Deneme Sınavı başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Exam $exam)
    {
        $this->authorize('view', $exam);
        $exam->load(['subjects.course', 'results.student.classroom', 'results.branchResults']);
        
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);
        $exam->load('subjects');
        $courses = Course::all();
        
        return view('admin.exams.edit', compact('exam', 'courses'));
    }

    public function update(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:TYT,AYT,LGS,YKS,Kurumsal Deneme,mock_exam,practice_exam,final_exam,quiz',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
            'exam_date' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_score' => 'required|numeric|min:1',
            'status' => 'required|string|in:draft,published,completed,cancelled',
            'subjects' => 'nullable|array',
            'subjects.*.course_id' => 'required|exists:courses,id',
            'subjects.*.question_count' => 'required|integer|min:1',
            'subjects.*.max_score' => 'required|numeric|min:1',
        ]);

        $this->service->updateExam($exam, $validated);

        return redirect()->route('admin.exams.index')->with('success', 'Sınav güncellendi.');
    }

    public function destroy(Exam $exam)
    {
        $this->authorize('delete', $exam);
        $this->service->deleteExam($exam);
        
        return redirect()->route('admin.exams.index')->with('success', 'Sınav silindi.');
    }
    
    public function analysis(Exam $exam)
    {
        $this->authorize('report', $exam);
        $analysis = $this->analysisService->getExamAnalysis($exam);
        
        return view('admin.exams.analysis', compact('exam', 'analysis'));
    }

    public function storeBranchResults(Request $request, ExamResult $result)
    {
        $validated = $request->validate([
            'branches' => 'required|array',
            'branches.*.correct' => 'required|integer|min:0',
            'branches.*.wrong' => 'required|integer|min:0',
            'branches.*.empty' => 'required|integer|min:0',
        ]);

        $examType = $result->exam?->type ?? 'TYT';
        $this->academicService->saveExamBranchResults($result, $validated['branches'], $examType);

        return redirect()->back()->with('success', '13 branş sınav sonuçları ve netleri başarıyla kaydedildi.');
    }

    public function studentAnalytics(Student $student)
    {
        $netGrowth = $this->academicService->getStudentNetGrowth($student->id);
        $comparisons = $this->academicService->getComparisonMetrics(
            $student->id,
            $student->branch_id ?? 1,
            $student->classroom_id
        );
        $studyProgramSummary = $this->academicService->getStudentStudyProgramSummary($student->id);

        return view('admin.students.academic_analytics', compact('student', 'netGrowth', 'comparisons', 'studyProgramSummary'));
    }
}
