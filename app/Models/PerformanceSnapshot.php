<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\TenantScoped;

class PerformanceSnapshot extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'branch_id',
        'student_id',
        'academic_term_id',
        'attendance_rate',
        'exam_average',
        'homework_completion',
        'late_submission_rate',
        'risk_score',
        'snapshot_date'
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'attendance_rate' => 'float',
        'exam_average' => 'float',
        'homework_completion' => 'float',
        'late_submission_rate' => 'float',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }
}
