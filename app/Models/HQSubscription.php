<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_subscriptions';

    protected $fillable = [
        'uuid',
        'tenant_id',
        'plan_id',
        'status',
        'starts_at',
        'expires_at',
        'cancelled_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
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
        return $this->belongsTo(HQPlan::class, 'plan_id');
    }

    public function items()
    {
        return $this->hasMany(HQSubscriptionItem::class, 'subscription_id');
    }

    public function invoices()
    {
        return $this->hasMany(HQInvoice::class, 'subscription_id');
    }
}
