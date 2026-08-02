<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    use TenantScoped;

    protected $fillable = [
        'branch_id',
        'payment_id',
        'invoice_number',
        'amount',
        'status',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'issued_at' => 'datetime',
        ];
    }

    public function payment()
    {
        return $this->belongsTo(SubscriptionPayment::class, 'payment_id');
    }
}
