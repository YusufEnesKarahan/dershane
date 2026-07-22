<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $fillable = ['exam_result_id', 'question_number', 'student_answer', 'correct_answer', 'is_correct'];
}
