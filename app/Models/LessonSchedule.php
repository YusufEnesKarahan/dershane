<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonSchedule extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'branch_id',
        'academic_term_id',
        'classroom_id',
        'course_id',
        'lesson_period_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonPeriod()
    {
        return $this->belongsTo(LessonPeriod::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function additionalTeachers()
    {
        return $this->belongsToMany(Teacher::class, 'lesson_schedule_teachers', 'lesson_schedule_id', 'teacher_id')
                    ->withTimestamps();
    }
}
