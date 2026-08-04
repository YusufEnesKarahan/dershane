<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Domain\Homework\Services\HomeworkManagementService;
use App\Domain\Tenant\Services\SubscriptionLimitService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HomeworkController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected HomeworkManagementService $homeworkService,
        protected SubscriptionLimitService $limitService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Homework::class);
        $homeworks = Homework::with(['course', 'classroom', 'teacher.user'])->orderByDesc('created_at')->get();
        return view('admin.homeworks.index', compact('homeworks'));
    }

    public function create()
    {
        $this->authorize('create', Homework::class);
        
        if (!$this->limitService->checkHomeworkLimit(auth()->user()->branch_id)) {
            return redirect()->route('admin.homeworks.index')->with('error', 'Ödev oluşturma limitinize ulaştınız. Lütfen paketinizi yükseltin.');
        }

        return view('admin.homeworks.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Homework::class);

        if (!$this->limitService->checkHomeworkLimit(auth()->user()->branch_id)) {
            return redirect()->route('admin.homeworks.index')->with('error', 'Ödev oluşturma limitinize ulaştınız.');
        }

        $validated = $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after:today',
            'allow_late_submission' => 'boolean',
            'max_score' => 'required|integer|min:1',
            'status' => 'required|in:draft,published',
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;
        $validated['allow_late_submission'] = $request->has('allow_late_submission');

        try {
            $this->homeworkService->createHomework($validated);
            return redirect()->route('admin.homeworks.index')->with('success', 'Ödev başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Homework $homework)
    {
        $this->authorize('update', $homework);
        return view('admin.homeworks.edit', compact('homework'));
    }

    public function update(Request $request, Homework $homework)
    {
        $this->authorize('update', $homework);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'allow_late_submission' => 'boolean',
            'max_score' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,closed',
        ]);
        
        $validated['allow_late_submission'] = $request->has('allow_late_submission');

        try {
            $this->homeworkService->updateHomework($homework, $validated);
            return redirect()->route('admin.homeworks.index')->with('success', 'Ödev güncellendi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Homework $homework)
    {
        $this->authorize('delete', $homework);
        $this->homeworkService->deleteHomework($homework);
        return redirect()->route('admin.homeworks.index')->with('success', 'Ödev silindi.');
    }

    public function publish(Homework $homework)
    {
        $this->authorize('update', $homework);
        $this->homeworkService->publishHomework($homework);
        return redirect()->back()->with('success', 'Ödev yayınlandı.');
    }

    public function close(Homework $homework)
    {
        $this->authorize('update', $homework);
        $this->homeworkService->closeHomework($homework);
        return redirect()->back()->with('success', 'Ödev kapatıldı.');
    }
}
