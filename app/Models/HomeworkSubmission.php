<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeworkSubmission extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $table = 'homework_submissions';

    protected $fillable = [
        'branch_id',
        'homework_id',
        'student_id',
        'submitted_at',
        'grade',
        'teacher_feedback',
        'status',
        'graded_by',
        'graded_at',
        'attachment_path',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function files()
    {
        return $this->hasMany(HomeworkFile::class);
    }
}
