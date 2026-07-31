<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_plans';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'type',
        'price',
        'billing_period',
        'limits',
        'features',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'limits' => 'array',
        'features' => 'array',
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
