<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherLeaveRequest extends Model
{
    protected $fillable = ['teacher_id', 'start_date', 'end_date', 'reason', 'status'];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
