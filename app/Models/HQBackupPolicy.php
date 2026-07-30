<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQBackupPolicy extends Model
{
    use HasFactory;

    protected $table = 'hq_backup_policies';

    protected $fillable = [
        'tenant_id',
        'system_instance_id',
        'hq_backup_storage_location_id',
        'name',
        'frequency',
        'retention_days',
        'backup_type',
        'is_active',
        'uuid',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class);
    }

    public function systemInstance()
    {
        return $this->belongsTo(HQSystemInstance::class);
    }

    public function jobs()
    {
        return $this->hasMany(HQBackupJob::class, 'backup_policy_id');
    }

    public function storageLocation()
    {
        return $this->belongsTo(HQBackupStorageLocation::class, 'hq_backup_storage_location_id');
    }

    public function retentionRules()
    {
        return $this->hasMany(HQBackupRetentionRule::class, 'hq_backup_policy_id');
    }
}
