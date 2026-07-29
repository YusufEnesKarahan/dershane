<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQConfigurationProfile extends Model
{
    use HasFactory;

    protected $table = 'hq_configuration_profiles';

    protected $fillable = [
        'name',
        'scope',
        'tenant_id',
        'system_instance_id',
        'environment',
        'description',
        'status',
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

    public function items()
    {
        return $this->hasMany(HQConfigurationItem::class, 'profile_id');
    }

    public function versions()
    {
        return $this->hasMany(HQConfigurationVersion::class, 'profile_id');
    }

    public function logs()
    {
        return $this->hasMany(HQConfigurationLog::class, 'profile_id');
    }
}
