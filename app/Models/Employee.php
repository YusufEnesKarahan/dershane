<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_no', 'user_id', 'department_id', 'position_id', 'first_name', 'last_name',
        'tc_no', 'birth_date', 'gender', 'phone', 'email', 'address', 'hire_date',
        'contract_type', 'employment_status', 'salary', 'iban', 'emergency_contact',
        'emergency_phone', 'notes'
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function advances()
    {
        return $this->hasMany(Advance::class);
    }

    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }
}
