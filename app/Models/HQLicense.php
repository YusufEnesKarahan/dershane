<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQLicense extends Model
{
    use HasFactory;

    protected $table = 'hq_licenses';

    protected $fillable = [
        'tenant_id',
        'system_instance_id',
        'license_key',
        'plan',
        'status',
        'starts_at',
        'expires_at',
        'features',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'features' => 'array',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->license_key)) {
                $model->license_key = 'LIC-' . strtoupper(Str::random(16));
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }

    public function systemInstance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'system_instance_id');
    }

    public function licenseFeatures()
    {
        return $this->hasMany(HQLicenseFeature::class, 'license_id');
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
