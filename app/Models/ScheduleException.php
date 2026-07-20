<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleException extends Model
{
    protected $fillable = [
        'class_schedule_id', 'exception_date', 'type',
        'make_up_date', 'make_up_start_time', 'make_up_end_time', 'reason'
    ];
}
