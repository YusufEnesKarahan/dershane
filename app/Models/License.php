<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'license_key',
        'status',
        'plan',
        'expires_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
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
}
