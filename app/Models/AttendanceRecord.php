<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\TenantScoped;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'branch_id',
        'attendance_session_id',
        'student_id',
        'classroom_id',
        'lesson_schedule_id',
        'teacher_id',
        'attendance_date',
        'status', // present, absent, late, excused
        'note',
        'created_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function schedule()
    {
        return $this->belongsTo(LessonSchedule::class, 'lesson_schedule_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
