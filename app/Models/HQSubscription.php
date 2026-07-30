<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQSubscription extends Model
{
    use HasFactory;

    protected $table = 'hq_subscriptions';

    protected $fillable = [
        'uuid',
        'tenant_id',
        'plan_id',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
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

    public function plan()
    {
        return $this->belongsTo(HQSubscriptionPlan::class, 'plan_id');
    }

    public function invoices()
    {
        return $this->hasMany(HQInvoice::class, 'subscription_id');
    }

    public function history()
    {
        return $this->hasMany(HQSubscriptionHistory::class, 'subscription_id');
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
