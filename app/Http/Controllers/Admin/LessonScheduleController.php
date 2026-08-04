<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonSchedule;
use App\Domain\Academic\Services\LessonScheduleManagementService;
use App\Domain\Tenant\Services\SubscriptionLimitService;
use Illuminate\Http\Request;

class LessonScheduleController extends Controller
{
    public function __construct(
        protected LessonScheduleManagementService $service,
        protected SubscriptionLimitService $limitService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', LessonSchedule::class);
        $schedules = LessonSchedule::with(['teacher', 'course', 'classroom'])
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $this->authorize('create', LessonSchedule::class);
        return view('admin.schedules.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', LessonSchedule::class);

        if (!$this->limitService->checkScheduleLimit(auth()->user()->branch_id)) {
            return redirect()->back()->with('error', 'Ders programı oluşturma limitine ulaştınız. Lütfen paketinizi yükseltin.');
        }

        $validated = $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:255',
            'additional_teachers' => 'nullable|array',
            'additional_teachers.*' => 'exists:teachers,id',
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;
        $validated['status'] = 'active';

        try {
            $this->service->createSchedule($validated);
            return redirect()->route('admin.schedules.index')->with('success', 'Ders programı başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(LessonSchedule $schedule)
    {
        $this->authorize('update', $schedule);
        return view('admin.schedules.edit', compact('schedule'));
    }

    public function update(Request $request, LessonSchedule $schedule)
    {
        $this->authorize('update', $schedule);

        $validated = $request->validate([
            'academic_term_id' => 'sometimes|exists:academic_terms,id',
            'classroom_id' => 'sometimes|exists:classrooms,id',
            'course_id' => 'sometimes|exists:courses,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'day_of_week' => 'sometimes|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:255',
            'additional_teachers' => 'nullable|array',
            'additional_teachers.*' => 'exists:teachers,id',
        ]);

        try {
            $this->service->updateSchedule($schedule, $validated);
            return redirect()->route('admin.schedules.index')->with('success', 'Ders programı güncellendi.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(LessonSchedule $schedule)
    {
        $this->authorize('delete', $schedule);

        $this->service->deleteSchedule($schedule);
        return redirect()->route('admin.schedules.index')->with('success', 'Ders programı silindi.');
    }
}
