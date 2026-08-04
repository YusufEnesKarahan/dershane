<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LessonScheduleTeacher extends Pivot
{
    protected $table = 'lesson_schedule_teachers';

    public $incrementing = true;

    protected $fillable = [
        'lesson_schedule_id',
        'teacher_id',
    ];
}
