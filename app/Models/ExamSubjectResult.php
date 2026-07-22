<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSubjectResult extends Model
{
    protected $fillable = ['exam_result_id', 'subject_name', 'correct_count', 'wrong_count', 'empty_count', 'net_count'];
}
