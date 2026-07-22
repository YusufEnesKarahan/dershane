<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\DTOs\Teacher\TeacherPerformanceDTO;
use App\Domain\Teacher\Actions\EvaluateTeacherPerformance;
use App\Domain\Teacher\Services\TeacherPerformanceService;
use Illuminate\Http\Request;

class TeacherPerformanceController extends Controller
{
    public function __construct(protected TeacherPerformanceService $performanceService) {}

    public function show($id)
    {
        $teacher = Teacher::findOrFail($id);
        $logs = $this->performanceService->getLogs((int) $id);
        return view('admin.teachers.performance', compact('teacher', 'logs'));
    }

    public function store(Request $request, EvaluateTeacherPerformance $action)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'metric_type' => 'required|string',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $dto = new TeacherPerformanceDTO(
            (int) $request->teacher_id,
            $request->metric_type,
            (float) $request->score,
            $request->comments
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Performans değerlendirme kaydı başarıyla eklendi.');
    }
}
