<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use TenantScoped;

    protected $fillable = [
        'branch_id',
        'subscription_id',
        'amount',
        'currency',
        'status',
        'gateway',
        'transaction_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'status' => \App\Domain\Billing\Enums\PaymentStatus::class,
        ];
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function invoice()
    {
        return $this->hasOne(SubscriptionInvoice::class, 'payment_id');
    }
}
