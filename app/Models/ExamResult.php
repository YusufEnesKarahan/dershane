<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;

class ExamResult extends Model
{
    use TenantScoped;

    protected $fillable = [
        'branch_id', 'exam_id', 'student_id', 'score', 'rank', 'notes'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
