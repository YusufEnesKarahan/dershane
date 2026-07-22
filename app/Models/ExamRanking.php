<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamRanking extends Model
{
    protected $fillable = ['exam_id', 'student_id', 'branch_id', 'score', 'branch_rank', 'global_rank'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
