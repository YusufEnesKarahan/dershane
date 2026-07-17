<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSchedule extends Model
{
    protected $fillable = ['teacher_id', 'classroom_id', 'course_id', 'date', 'start_time', 'end_time'];
    protected $casts = [
        'date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
