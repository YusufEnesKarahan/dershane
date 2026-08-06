<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\TenantScoped;

class Installment extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'branch_id',
        'payment_plan_id',
        'installment_no',
        'due_date',
        'amount',
        'paid_amount',
        'remaining_amount',
        'status'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($installment) {
            if (is_null($installment->remaining_amount)) {
                $installment->remaining_amount = max(0, (float)$installment->amount - (float)($installment->paid_amount ?? 0));
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function paymentPlan()
    {
        return $this->belongsTo(PaymentPlan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
