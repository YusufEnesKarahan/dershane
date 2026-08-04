<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\TenantScoped;

class Payment extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'branch_id',
        'student_id',
        'installment_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_no',
        'received_by',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
