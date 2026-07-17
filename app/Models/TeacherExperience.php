<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherExperience extends Model
{
    protected $fillable = ['teacher_id', 'company', 'role', 'start_date', 'end_date', 'description'];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
