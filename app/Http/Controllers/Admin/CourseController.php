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
        $courses = Course::with(['teachers.user', 'level', 'currentPricing'])->paginate(15);
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
        $course = null;

        return view('admin.courses.edit', compact('course', 'levels', 'teachers', 'branches', 'prerequisites'));
    }

    public function store(Request $request, CreateCourseAction $action)
    {
        $this->authorize('create', Course::class);

        $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'primary_teacher_id' => 'nullable|exists:teachers,id',
            'assistant_teacher_ids' => 'nullable|array',
            'assistant_teacher_ids.*' => 'exists:teachers,id',
        ]);

        $dto = new CreateCourseDTO(
            $request->code,
            $request->name,
            $request->description,
            $request->course_level_id ? (int) $request->course_level_id : null,
            $request->duration,
            (int) ($request->capacity ?? 0),
            $request->status ?? 'Published',
            (bool) ($request->is_active ?? true),
            $request->cover_image
        );

        try {
            $course = $action->execute($dto);
            $this->pricingService->setPrice($course, (float) $request->price);

            // Sync teachers with primary & assistant roles
            $this->syncCourseTeachers($course, $request->input('primary_teacher_id'), $request->input('assistant_teacher_ids', []));

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('admin.courses.index')->with('success', 'Ders kaydı ve öğretmen ataması başarıyla oluşturuldu.');
    }

    public function edit(Course $course)
    {
        $this->authorize('update', Course::class);

        $course->load(['teachers.user', 'branches', 'level']);
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
            'primary_teacher_id' => 'nullable|exists:teachers,id',
            'assistant_teacher_ids' => 'nullable|array',
            'assistant_teacher_ids.*' => 'exists:teachers,id',
        ]);

        $dto = UpdateCourseDTO::fromRequest($request->all());

        $action->execute($course, $dto);

        // Update pricing history records
        $this->pricingService->setPrice($course, (float) $request->price);

        // Sync teachers with primary & assistant roles
        $this->syncCourseTeachers($course, $request->input('primary_teacher_id'), $request->input('assistant_teacher_ids', []));

        return redirect()->route('admin.courses.index')->with('success', 'Ders bilgileri ve öğretmen atamaları başarıyla güncellendi.');
    }

    public function destroy(Course $course, DeleteCourseAction $action)
    {
        $this->authorize('delete', Course::class);

        $action->execute($course);

        return redirect()->route('admin.courses.index')->with('success', 'Ders başarıyla silindi.');
    }

    public function analytics()
    {
        $this->authorize('viewAny', Course::class);

        $analytics = $this->analyticsService->getAnalyticsSummary();
        $popular = Course::with(['level', 'currentPricing', 'teachers.user'])->take(5)->get();

        return view('admin.courses.analytics', compact('analytics', 'popular'));
    }

    private function syncCourseTeachers(Course $course, ?int $primaryTeacherId, array $assistantTeacherIds): void
    {
        $syncData = [];

        if ($primaryTeacherId) {
            $syncData[$primaryTeacherId] = ['is_primary' => true, 'role' => 'Primary'];
        }

        foreach ($assistantTeacherIds as $asstId) {
            $asstId = (int) $asstId;
            if ($asstId && $asstId !== (int) $primaryTeacherId) {
                $syncData[$asstId] = ['is_primary' => false, 'role' => 'Assistant'];
            }
        }

        $course->teachers()->sync($syncData);
    }
}
