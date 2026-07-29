<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HQUpdateJob extends Model
{
    protected $table = 'hq_update_jobs';

    protected $fillable = [
        'uuid',
        'version_id',
        'system_instance_id',
        'tenant_id',
        'target_type',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'progress',
        'result',
        'error_message'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer',
        'result' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function version()
    {
        return $this->belongsTo(HQVersion::class, 'version_id');
    }

    public function systemInstance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'system_instance_id');
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
