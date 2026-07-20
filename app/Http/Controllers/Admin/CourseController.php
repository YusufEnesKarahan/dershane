<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\Teacher;
use App\Models\Branch;
use App\DTOs\Course\CourseFilterDTO;
use App\DTOs\Course\CreateCourseDTO;
use App\DTOs\Course\UpdateCourseDTO;
use App\Domain\Course\Actions\CreateCourseAction;
use App\Domain\Course\Actions\UpdateCourseAction;
use App\Domain\Course\Actions\DeleteCourseAction;
use App\Core\Repositories\Interfaces\CourseRepositoryInterface;
use App\Domain\Course\Services\CoursePricingService;
use App\Domain\Course\Services\CourseAnalyticsService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(
        protected CourseRepositoryInterface $repository,
        protected CoursePricingService $pricingService,
        protected CourseAnalyticsService $analyticsService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Course::class);

        $filters = CourseFilterDTO::fromRequest($request->all());
        $courses = $this->repository->paginate(15, (array) $filters);
        $levels = CourseLevel::all();

        return view('admin.courses.index', compact('courses', 'levels'));
    }

    public function create()
    {
        $this->authorize('create', Course::class);

        $levels = CourseLevel::all();
        $teachers = Teacher::with('user')->get();
        $branches = Branch::all();
        $prerequisites = Course::all();

        return view('admin.courses.edit', compact('levels', 'teachers', 'branches', 'prerequisites'));
    }

    public function store(Request $request, CreateCourseAction $action)
    {
        $this->authorize('create', Course::class);

        $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $dto = new CreateCourseDTO(
            $request->code,
            $request->name,
            $request->description,
            $request->course_level_id ? (int) $request->course_level_id : null,
            $request->duration,
            (int) ($request->capacity ?? 0),
            $request->status ?? 'Draft',
            (bool) ($request->is_active ?? true),
            $request->cover_image
        );

        try {
            $course = $action->execute($dto);
            $this->pricingService->setPrice($course, (float) $request->price);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('admin.courses.edit', $course->id)->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $this->authorize('update', Course::class);

        $levels = CourseLevel::all();
        $teachers = Teacher::with('user')->get();
        $branches = Branch::all();
        $prerequisites = Course::where('id', '<>', $course->id)->get();

        return view('admin.courses.edit', compact('course', 'levels', 'teachers', 'branches', 'prerequisites'));
    }

    public function update(Request $request, Course $course, UpdateCourseAction $action)
    {
        $this->authorize('update', Course::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $dto = UpdateCourseDTO::fromRequest($request->all());

        $action->execute($course, $dto);

        // Update pricing history records
        $this->pricingService->setPrice($course, (float) $request->price);

        return redirect()->route('admin.courses.edit', $course->id)->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course, DeleteCourseAction $action)
    {
        $this->authorize('delete', Course::class);

        $action->execute($course);

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }

    public function analytics()
    {
        $this->authorize('viewAny', Course::class);

        $analytics = $this->analyticsService->getAnalyticsSummary();
        $popular = Course::with(['level', 'currentPricing'])->take(5)->get();

        return view('admin.courses.analytics', compact('analytics', 'popular'));
    }
}
