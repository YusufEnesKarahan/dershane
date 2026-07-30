<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQPayment extends Model
{
    use HasFactory;

    protected $table = 'hq_payments';

    protected $fillable = [
        'invoice_id',
        'provider',
        'transaction_id',
        'amount',
        'status',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(HQInvoice::class, 'invoice_id');
    }
}
