<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    protected $table = 'employee_attendances';

    protected $fillable = [
        'employee_id', 'date', 'check_in', 'check_out', 'worked_minutes', 'overtime_minutes',
        'late_minutes'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
