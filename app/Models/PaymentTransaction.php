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
        'transaction_type',
        'amount',
        'description'
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
