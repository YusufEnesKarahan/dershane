<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    protected $fillable = ['teacher_id', 'bio_extended', 'office_hours', 'room_number'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
