<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'subscription_payment_id',
        'gateway',
        'transaction_id',
        'idempotency_key',
        'status',
        'payload',
        'response',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'response' => 'array',
        ];
    }

    public function payment()
    {
        return $this->belongsTo(SubscriptionPayment::class, 'subscription_payment_id');
    }
}
