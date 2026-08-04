<?php

namespace App\Domain\Schedule\Services;

use App\Models\LessonPeriod;
use Illuminate\Support\Facades\DB;

class LessonPeriodService
{
    public function getAllPeriods()
    {
        return LessonPeriod::orderBy('start_time')->get();
    }

    public function createPeriod(array $data): LessonPeriod
    {
        return DB::transaction(function () use ($data) {
            return LessonPeriod::create($data);
        });
    }

    public function updatePeriod(LessonPeriod $period, array $data): LessonPeriod
    {
        return DB::transaction(function () use ($period, $data) {
            $period->update($data);
            return $period->fresh();
        });
    }

    public function deletePeriod(LessonPeriod $period): bool
    {
        return DB::transaction(function () use ($period) {
            return $period->delete();
        });
    }
}
