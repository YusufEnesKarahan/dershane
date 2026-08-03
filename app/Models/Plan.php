<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Plan extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'price',
        'billing_period',
        'billing_cycle',
        'trial_days',
        'max_students',
        'max_users',
        'max_teachers',
        'max_classrooms',
        'is_active',
        'features',
        'limits',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'trial_days' => 'integer',
            'max_students' => 'integer',
            'max_users' => 'integer',
            'max_teachers' => 'integer',
            'max_classrooms' => 'integer',
            'features' => 'array',
            'limits' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $plan): void {
            if (empty($plan->uuid)) {
                $plan->uuid = (string) Str::uuid();
            }
        });
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
