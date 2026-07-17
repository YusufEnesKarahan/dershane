<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherContract extends Model
{
    protected $fillable = ['teacher_id', 'start_date', 'end_date', 'employment_type', 'status'];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
