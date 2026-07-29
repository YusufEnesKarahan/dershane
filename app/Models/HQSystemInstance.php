<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQSystemInstance extends Model
{
    use HasFactory;

    protected $table = 'hq_system_instances';

    protected $fillable = [
        'tenant_id',
        'system_uuid',
        'system_name',
        'system_version',
        'environment',
        'last_seen_at',
        'status',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }

    public function telemetry()
    {
        return $this->hasMany(HQTelemetryRecord::class, 'system_instance_id');
    }

    public function commands()
    {
        return $this->hasMany(HQCentralCommand::class, 'system_instance_id');
    }

    public function logs()
    {
        return $this->hasMany(HQCentralSyncLog::class, 'system_instance_id');
    }

    public function licenses()
    {
        return $this->hasMany(HQLicense::class, 'system_instance_id');
    }

    public function currentLicense()
    {
        return $this->hasOne(HQLicense::class, 'system_instance_id')
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('starts_at');
    }
}
