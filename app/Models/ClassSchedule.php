<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    protected $fillable = [
        'classroom_id', 'teacher_id', 'course_id', 'academic_term_id',
        'day_of_week', 'start_time', 'end_time', 'color_code', 'is_active'
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function term()
    {
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }

    public function exceptions()
    {
        return $this->hasMany(ScheduleException::class);
    }
}
