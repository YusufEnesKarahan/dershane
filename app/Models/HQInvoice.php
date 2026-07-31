<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HQInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_invoices';

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'amount',
        'status',
        'invoice_number',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }

    public function subscription()
    {
        return $this->belongsTo(HQSubscription::class, 'subscription_id');
    }
}
