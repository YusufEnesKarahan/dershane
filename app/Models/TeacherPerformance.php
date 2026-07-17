<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherPerformance extends Model
{
    protected $table = 'teacher_performance';
    protected $fillable = ['teacher_id', 'attendance_rate', 'student_satisfaction', 'lesson_count', 'feedback_score', 'kpi_month'];
}
