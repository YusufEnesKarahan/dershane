<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes, TenantScoped;

    protected $fillable = [
        'invoice_number', 'student_id', 'issue_date', 'due_date',
        'total_amount', 'paid_amount', 'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function debt()
    {
        return $this->hasOne(StudentDebt::class);
    }
}

