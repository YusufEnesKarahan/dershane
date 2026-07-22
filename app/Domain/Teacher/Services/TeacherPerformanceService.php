<?php

namespace App\Domain\Teacher\Services;

use App\DTOs\Teacher\TeacherPerformanceDTO;
use App\Models\TeacherPerformanceLog;
use Illuminate\Support\Collection;

class TeacherPerformanceService
{
    public function logPerformance(TeacherPerformanceDTO $dto): TeacherPerformanceLog
    {
        return TeacherPerformanceLog::create([
            'teacher_id' => $dto->teacher_id,
            'metric_type' => $dto->metric_type,
            'score' => $dto->score,
            'comments' => $dto->comments,
            'evaluated_at' => now(),
        ]);
    }

    public function getLogs(int $teacherId): Collection
    {
        return TeacherPerformanceLog::where('teacher_id', $teacherId)
            ->orderBy('evaluated_at', 'desc')
            ->get();
    }
}
