<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\DTOs\Exam\CreateExamDTO;
use App\Domain\Exam\Actions\CreateExamAction;
use App\Core\Repositories\Interfaces\ExamRepositoryInterface;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(protected ExamRepositoryInterface $repository) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Exam::class);

        $exams = $this->repository->paginate(15, $request->all());

        return view('admin.exams.index', compact('exams'));
    }

    public function store(Request $request, CreateExamAction $action)
    {
        $this->authorize('create', Exam::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'exam_date' => 'required|date',
        ]);

        $dto = new CreateExamDTO(
            $request->title,
            $request->code,
            $request->exam_type ?? 'Trial',
            $request->exam_date,
            (int) ($request->total_questions ?? 120),
            (int) ($request->duration_minutes ?? 135)
        );

        try {
            $exam = $action->execute($dto);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('admin.exams.results.index', $exam->id)->with('success', 'Sınav oluşturuldu. Sonuçları girebilirsiniz.');
    }
}
