<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Branch;
use App\Models\User;
use App\DTOs\Teacher\CreateTeacherDTO;
use App\DTOs\Teacher\UpdateTeacherDTO;
use App\Domain\Teacher\Actions\CreateTeacher;
use App\Domain\Teacher\Actions\UpdateTeacherProfile;
use App\Domain\Teacher\Services\TeacherAnalyticsService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(protected TeacherAnalyticsService $analyticsService) {}

    public function index(Request $request)
    {
        $teachers = Teacher::with(['user', 'branch'])->paginate(15);
        $users = User::all();
        $branches = Branch::all();

        return view('admin.teachers.index', compact('teachers', 'users', 'branches'));
    }

    public function store(Request $request, CreateTeacher $action)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:teachers,user_id',
            'branch_id' => 'nullable|exists:branches,id',
            'title' => 'required|string',
            'specialties' => 'required|string',
        ]);

        $dto = new CreateTeacherDTO(
            (int) $request->user_id,
            $request->branch_id ? (int) $request->branch_id : null,
            $request->title,
            $request->bio,
            $request->specialties,
            $request->education,
            (int) $request->experience_years
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Eğitmen/Öğretmen kaydı başarıyla oluşturuldu.');
    }

    public function edit($id)
    {
        $teacher = Teacher::findOrFail($id);
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, $id, UpdateTeacherProfile $action)
    {
        $request->validate([
            'title' => 'required|string',
            'specialties' => 'required|string',
        ]);

        $dto = new UpdateTeacherDTO(
            $request->title,
            $request->bio,
            $request->specialties,
            $request->education,
            (int) $request->experience_years
        );

        $action->execute((int) $id, $dto);

        return redirect()->route('admin.teachers.index')->with('success', 'Öğretmen profili güncellendi.');
    }

    public function analytics(Teacher $teacher)
    {
        $analytics = $this->analyticsService->getAnalyticsSummary($teacher->id);
        return view('admin.teachers.analytics', compact('analytics'));
    }
}
