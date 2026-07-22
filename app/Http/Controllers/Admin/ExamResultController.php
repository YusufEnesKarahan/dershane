<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Student;
use App\DTOs\Exam\ExamResultDTO;
use App\Domain\Exam\Actions\RecordExamResultsAction;
use App\Domain\Exam\Services\ExamAnalyticsService;
use App\Core\Repositories\Interfaces\ExamResultRepositoryInterface;
use Illuminate\Http\Request;

class ExamResultController extends Controller
{
    public function __construct(
        protected ExamResultRepositoryInterface $repository,
        protected ExamAnalyticsService $analyticsService
    ) {}

    public function index(Exam $exam)
    {
        $results = $this->repository->getByExam($exam->id);
        $students = Student::all();

        return view('admin.exams.results', compact('exam', 'results', 'students'));
    }

    public function store(Request $request, Exam $exam, RecordExamResultsAction $action)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'total_correct' => 'required|integer',
            'total_wrong' => 'required|integer',
            'total_empty' => 'required|integer',
        ]);

        $dto = new ExamResultDTO(
            $exam->id,
            (int) $request->student_id,
            (int) $request->total_correct,
            (int) $request->total_wrong,
            (int) $request->total_empty,
            (bool) ($request->is_absent ?? false)
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Sınav sonucu kaydedildi ve sıralama güncellendi.');
    }

    public function analytics()
    {
        $summary = $this->analyticsService->getSummary();
        $exams = Exam::with(['results.student'])->orderBy('exam_date', 'desc')->get();

        return view('admin.exams.analytics', compact('summary', 'exams'));
    }
}
