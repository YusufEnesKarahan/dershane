<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAvailability extends Model
{
    protected $table = 'teacher_availability';
    protected $fillable = ['teacher_id', 'day_of_week', 'start_time', 'end_time', 'is_recurring'];
}
