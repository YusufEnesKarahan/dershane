<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQInvoice extends Model
{
    use HasFactory;

    protected $table = 'hq_invoices';

    protected $fillable = [
        'uuid',
        'tenant_id',
        'subscription_id',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'issued_at',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }

    public function subscription()
    {
        return $this->belongsTo(HQSubscription::class, 'subscription_id');
    }

    public function payments()
    {
        return $this->hasMany(HQPayment::class, 'invoice_id');
    }
}
