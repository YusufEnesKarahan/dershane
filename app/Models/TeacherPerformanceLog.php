<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherPerformanceLog extends Model
{
    protected $fillable = ['teacher_id', 'metric_type', 'score', 'comments', 'evaluated_at'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
