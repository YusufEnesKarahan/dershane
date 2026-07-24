<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id', 'month', 'year', 'gross_salary', 'bonus', 'overtime_amount',
        'deductions', 'tax', 'insurance', 'net_salary', 'payment_date', 'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
