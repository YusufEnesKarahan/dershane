<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQAlert extends Model
{
    use HasFactory;

    protected $table = 'hq_alerts';

    protected $fillable = [
        'uuid',
        'rule_id',
        'tenant_id',
        'system_instance_id',
        'title',
        'message',
        'severity',
        'status',
        'triggered_at',
        'resolved_at',
        'metadata',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
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

    public function rule()
    {
        return $this->belongsTo(HQAlertRule::class, 'rule_id');
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }

    public function systemInstance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'system_instance_id');
    }

    public function notificationLogs()
    {
        return $this->hasMany(HQNotificationLog::class, 'alert_id');
    }
}
