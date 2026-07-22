<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDebt extends Model
{
    protected $fillable = ['student_id', 'invoice_id', 'amount', 'remaining_amount', 'due_date', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
