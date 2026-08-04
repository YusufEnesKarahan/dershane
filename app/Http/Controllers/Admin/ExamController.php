<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamType;
use App\Domain\Exam\Services\ExamManagementService;
use App\Domain\Tenant\Services\SubscriptionLimitService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(
        protected ExamManagementService $service,
        protected SubscriptionLimitService $limitService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Exam::class);
        $exams = Exam::with(['type', 'classroom'])->latest()->paginate(15);
        $totalExams = Exam::count();
        // Since we are simulating upcoming exams for dashboard:
        $upcomingExams = Exam::where('exam_date', '>=', now())->count();
        
        return view('admin.exams.index', compact('exams', 'totalExams', 'upcomingExams'));
    }

    public function create()
    {
        $this->authorize('create', Exam::class);
        $examTypes = ExamType::all();
        $classrooms = \App\Models\Classroom::all();
        
        return view('admin.exams.create', compact('examTypes', 'classrooms'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Exam::class);

        if (!$this->limitService->checkExamLimit(auth()->user()->branch_id)) {
            return redirect()->back()->with('error', 'Sınav oluşturma limitine ulaştınız. Lütfen paketinizi yükseltin.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_type_id' => 'required|exists:exam_types,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'exam_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'total_score' => 'required|integer|min:1',
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'published';

        $exam = $this->service->createExam($validated);

        return redirect()->route('admin.exams.index')->with('success', 'Sınav başarıyla oluşturuldu.');
    }

    public function show(Exam $exam)
    {
        $this->authorize('view', $exam);
        $exam->load(['type', 'classroom', 'results.student']);
        $stats = $this->service->getExamStatistics($exam);
        
        return view('admin.exams.show', compact('exam', 'stats'));
    }

    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);
        $examTypes = ExamType::all();
        $classrooms = \App\Models\Classroom::all();
        
        return view('admin.exams.edit', compact('exam', 'examTypes', 'classrooms'));
    }

    public function update(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_type_id' => 'required|exists:exam_types,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'exam_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'total_score' => 'required|integer|min:1',
            'status' => 'required|string|in:draft,published,completed',
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

    public function results(Exam $exam)
    {
        $this->authorize('results', $exam);
        
        // Load students either from the assigned classroom or all students in the branch if none assigned.
        if ($exam->classroom_id) {
            $students = \App\Models\Student::where('classroom_id', $exam->classroom_id)->get();
        } else {
            $students = \App\Models\Student::all();
        }

        $existingResults = $exam->results()->get()->keyBy('student_id');

        return view('admin.exams.results', compact('exam', 'students', 'existingResults'));
    }

    public function saveResults(Request $request, Exam $exam)
    {
        $this->authorize('results', $exam);

        $validated = $request->validate([
            'results' => 'required|array',
            'results.*.student_id' => 'required|exists:students,id',
            'results.*.score' => 'required|numeric|min:0',
            'results.*.notes' => 'nullable|string',
        ]);

        $this->service->enterResults($exam, $validated['results']);

        return redirect()->route('admin.exams.show', $exam)->with('success', 'Sınav sonuçları kaydedildi ve sıralama güncellendi.');
    }
}
