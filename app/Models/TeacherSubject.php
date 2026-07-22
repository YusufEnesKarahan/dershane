<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    protected $fillable = ['teacher_id', 'subject_name', 'code'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
