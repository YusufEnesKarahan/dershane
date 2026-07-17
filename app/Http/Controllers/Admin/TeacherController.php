<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Branch;
use App\Models\User;
use App\DTOs\Teacher\TeacherFilterDTO;
use App\DTOs\Teacher\CreateTeacherDTO;
use App\DTOs\Teacher\UpdateTeacherDTO;
use App\Domain\Teacher\Actions\CreateTeacherAction;
use App\Domain\Teacher\Actions\UpdateTeacherAction;
use App\Domain\Teacher\Actions\DeleteTeacherAction;
use App\Core\Repositories\Interfaces\TeacherRepositoryInterface;
use App\Domain\Teacher\Services\TeacherAnalyticsService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(
        protected TeacherRepositoryInterface $repository,
        protected TeacherAnalyticsService $analyticsService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Teacher::class);

        $filters = TeacherFilterDTO::fromRequest($request->all());
        $teachers = $this->repository->paginate(15, (array) $filters);
        $branches = Branch::all();

        return view('admin.teachers.index', compact('teachers', 'branches'));
    }

    public function create()
    {
        $this->authorize('create', Teacher::class);

        $branches = Branch::all();
        // Get users who are not teachers yet
        $users = User::whereNotExists(function ($q) {
            $q->select('id')->from('teachers')->whereColumn('teachers.user_id', 'users.id');
        })->get();

        return view('admin.teachers.edit', compact('branches', 'users'));
    }

    public function store(Request $request, CreateTeacherAction $action)
    {
        $this->authorize('create', Teacher::class);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'title' => 'required|string|max:255',
        ]);

        $dto = new CreateTeacherDTO(
            (int) $request->user_id,
            $request->branch_id ? (int) $request->branch_id : null,
            $request->title,
            $request->bio,
            $request->specialties,
            $request->education,
            (int) ($request->experience_years ?? 0),
            $request->emergency_contact,
            $request->status ?? 'Active'
        );

        $teacher = $action->execute($dto);

        return redirect()->route('admin.teachers.edit', $teacher->id)->with('success', 'Teacher profile created successfully.');
    }

    public function edit(Teacher $teacher)
    {
        $this->authorize('update', Teacher::class);

        $branches = Branch::all();
        $analytics = $this->analyticsService->getAnalyticsSummary($teacher);

        return view('admin.teachers.edit', compact('teacher', 'branches', 'analytics'));
    }

    public function update(Request $request, Teacher $teacher, UpdateTeacherAction $action)
    {
        $this->authorize('update', Teacher::class);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $dto = UpdateTeacherDTO::fromRequest($request->all());

        $action->execute($teacher, $dto);

        return redirect()->route('admin.teachers.edit', $teacher->id)->with('success', 'Teacher profile updated successfully.');
    }

    public function destroy(Teacher $teacher, DeleteTeacherAction $action)
    {
        $this->authorize('delete', Teacher::class);

        $action->execute($teacher);

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher profile deleted successfully.');
    }

    public function analytics()
    {
        $this->authorize('viewAny', Teacher::class);

        $popular = Teacher::with('user')->take(5)->get();

        return view('admin.teachers.analytics', compact('popular'));
    }
}
