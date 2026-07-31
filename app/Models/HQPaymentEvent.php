<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQPaymentEvent extends Model
{
    use HasFactory;

    protected $table = 'hq_payment_events';

    protected $fillable = [
        'tenant_id',
        'provider',
        'event_type',
        'payload',
        'status',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
