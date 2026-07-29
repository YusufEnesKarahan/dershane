<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQAuditLog extends Model
{
    use HasFactory;

    protected $table = 'hq_audit_logs';
    public $timestamps = false; // Only created_at is used

    protected $fillable = [
        'user_id',
        'tenant_id',
        'system_instance_id',
        'action',
        'category',
        'severity',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->created_at)) {
                $model->created_at = $model->freshTimestamp();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class);
    }

    public function systemInstance()
    {
        return $this->belongsTo(HQSystemInstance::class);
    }
}
