<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'license_id',
        'branch_id',
        'plan_id',
        'status',
        'started_at',
        'starts_at',
        'ends_at',
        'expires_at',
        'trial_ends_at',
        'canceled_at',
        'cancelled_at',
        'cancellation_reason',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'expires_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'canceled_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    protected $attributes = [
        'status' => 'trial',
    ];

    public function license()
    {
        return $this->belongsTo(License::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function logs()
    {
        return $this->hasMany(SubscriptionLog::class);
    }

    public function histories()
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeTenant($query)
    {
        return $query->whereNotNull('branch_id');
    }

    public function scopeSystem($query)
    {
        return $query->whereNull('branch_id');
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function isActive(): bool
    {
        $expiry = $this->expires_at ?? $this->ends_at;

        return $this->status === 'active' && (!$expiry || !$expiry->isPast());
    }

    public function isTrialing(): bool
    {
        return in_array($this->status, ['trial', 'trialing'], true) && (!$this->trial_ends_at || !$this->trial_ends_at->isPast());
    }
}
