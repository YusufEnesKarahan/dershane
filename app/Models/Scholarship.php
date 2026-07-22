<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = ['student_id', 'title', 'percentage', 'is_active'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
