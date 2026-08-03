<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;

class AttendanceSession extends Model
{
    use TenantScoped;
    protected $fillable = [
        'branch_id', 'class_schedule_id', 'classroom_id', 'course_id', 'teacher_id',
        'session_date', 'start_time', 'end_time', 'status', 'created_by'
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}

