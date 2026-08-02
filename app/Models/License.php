<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'license_key',
        'status',
        'plan',
        'plan_id',
        'starts_at',
        'expires_at',
        'trial_ends_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function planModel()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)->whereNull('branch_id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->whereNull('branch_id')->latestOfMany();
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']) && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        if (in_array($this->status, ['expired', 'cancelled'])) {
            return true;
        }

        if ($this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isPast()) {
            return true;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return true;
        }

        return false;
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial' && !$this->isExpired();
    }

    public function isDemo(): bool
    {
        return $this->status === 'demo';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
