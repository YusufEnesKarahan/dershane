<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Homework extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $table = 'homeworks';

    protected $fillable = [
        'branch_id',
        'academic_term_id',
        'classroom_id',
        'course_id',
        'teacher_id',
        'title',
        'description',
        'homework_type',
        'assigned_date',
        'publish_at',
        'due_date',
        'allow_late_submission',
        'max_score',
        'status',
        'attachment_path',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'publish_at' => 'datetime',
        'due_date' => 'datetime',
        'allow_late_submission' => 'boolean',
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

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function submissions()
    {
        return $this->hasMany(HomeworkSubmission::class);
    }

    public function comments()
    {
        return $this->hasMany(HomeworkComment::class);
    }

    public function files()
    {
        return $this->hasMany(HomeworkFile::class);
    }
}
