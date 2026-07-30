<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQSubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'hq_subscription_plans';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'billing_period',
        'price',
        'currency',
        'limits',
        'features',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'limits' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
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

    public function subscriptions()
    {
        return $this->hasMany(HQSubscription::class, 'plan_id');
    }
}
