<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $fillable = [
        'exam_id', 'student_id', 'total_correct', 'total_wrong', 'total_empty',
        'total_net', 'score', 'branch_rank', 'global_rank', 'is_absent'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function subjectResults()
    {
        return $this->hasMany(ExamSubjectResult::class);
    }
}
