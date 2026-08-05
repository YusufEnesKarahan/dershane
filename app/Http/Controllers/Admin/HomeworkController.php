<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\AcademicTerm;
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
        $homeworks = Homework::with(['course', 'classroom', 'teacher.user', 'submissions'])->orderByDesc('created_at')->get();
        return view('admin.homeworks.index', compact('homeworks'));
    }

    public function create()
    {
        $this->authorize('create', Homework::class);
        
        try {
            $this->limitService->checkHomeworkLimit(auth()->user()->branch_id ?? 1);
        } catch (\Exception $e) {
            return redirect()->route('admin.homeworks.index')->with('error', $e->getMessage());
        }

        $classrooms = Classroom::all();
        $courses = Course::all();
        $teachers = Teacher::with('user')->get();
        $academicTerms = AcademicTerm::all();

        return view('admin.homeworks.create', compact('classrooms', 'courses', 'teachers', 'academicTerms'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Homework::class);

        $validated = $request->validate([
            'academic_term_id' => 'nullable|exists:academic_terms,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'title' => 'required|string|max:255',
            'week_number' => 'nullable|integer|min:1|max:52',
            'start_date' => 'nullable|date',
            'due_date' => 'required|date',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'source_book' => 'nullable|string|max:255',
            'page_range' => 'nullable|string|max:100',
            'video_url' => 'nullable|url|max:500',
            'attachment_path' => 'nullable|string|max:500',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'estimated_minutes' => 'nullable|integer|min:5|max:600',
            'status' => 'required|in:draft,published',
        ]);

        $validated['branch_id'] = auth()->user()->branch_id ?? 1;
        $validated['assigned_date'] = $request->input('start_date', now()->format('Y-m-d'));
        $validated['max_score'] = 100;
        $validated['academic_term_id'] = $validated['academic_term_id'] ?? AcademicTerm::where('is_active', true)->value('id') ?? 1;
        $validated['priority'] = $validated['priority'] ?? 'medium';
        $validated['estimated_minutes'] = $validated['estimated_minutes'] ?? 45;

        try {
            $this->limitService->checkHomeworkLimit($validated['branch_id']);
            $this->homeworkService->createHomework($validated);
            return redirect()->route('admin.homeworks.index')->with('success', 'Haftalık Çalışma Programı başarıyla oluşturuldu ve yayınlandı.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Homework $homework)
    {
        $this->authorize('update', $homework);

        $classrooms = Classroom::all();
        $courses = Course::all();
        $teachers = Teacher::with('user')->get();
        $academicTerms = AcademicTerm::all();

        return view('admin.homeworks.edit', compact('homework', 'classrooms', 'courses', 'teachers', 'academicTerms'));
    }

    public function update(Request $request, Homework $homework)
    {
        $this->authorize('update', $homework);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'week_number' => 'nullable|integer|min:1|max:52',
            'start_date' => 'nullable|date',
            'due_date' => 'required|date',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'source_book' => 'nullable|string|max:255',
            'page_range' => 'nullable|string|max:100',
            'video_url' => 'nullable|url|max:500',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'estimated_minutes' => 'nullable|integer|min:5|max:600',
            'status' => 'required|in:draft,published,completed',
        ]);

        try {
            $this->homeworkService->updateHomework($homework, $validated);
            return redirect()->route('admin.homeworks.index')->with('success', 'Haftalık Çalışma Programı güncellendi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Homework $homework)
    {
        $this->authorize('delete', $homework);
        $this->homeworkService->deleteHomework($homework);
        return redirect()->route('admin.homeworks.index')->with('success', 'Çalışma programı silindi.');
    }

    public function publish(Homework $homework)
    {
        $this->authorize('update', $homework);
        $this->homeworkService->publishHomework($homework);
        return redirect()->back()->with('success', 'Çalışma programı yayınlandı.');
    }

    public function updateTaskProgress(Request $request, HomeworkSubmission $submission)
    {
        $validated = $request->validate([
            'task_status' => 'required|in:Not Started,In Progress,Completed',
        ]);

        $progressMap = [
            'Not Started' => 0,
            'In Progress' => 50,
            'Completed' => 100,
        ];

        $status = $validated['task_status'];

        $submission->update([
            'task_status' => $status,
            'progress_percentage' => $progressMap[$status] ?? 0,
            'status' => $status === 'Completed' ? 'submitted' : 'pending',
            'submitted_at' => $status === 'Completed' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Görev durumu ve ilerleme yüzdesi güncellendi.');
    }
}
