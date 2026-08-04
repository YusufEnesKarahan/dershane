<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;

class PaymentTransaction extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'branch_id',
        'payment_id',
        'subscription_payment_id',
        'gateway',
        'transaction_id',
        'idempotency_key',
        'status',
        'transaction_type',
        'amount',
        'description',
        'payload',
        'response'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
