<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'title', 'code', 'exam_type', 'exam_date', 'total_questions', 'duration_minutes', 'is_published'
    ];

    public function sessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function rankings()
    {
        return $this->hasMany(ExamRanking::class);
    }
}
