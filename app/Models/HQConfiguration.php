<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQConfiguration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_configurations';

    protected $fillable = [
        'uuid',
        'group_id',
        'tenant_id',
        'key',
        'value',
        'type',
        'is_encrypted',
        'description',
    ];

    protected $casts = [
        'value' => 'json',
        'is_encrypted' => 'boolean',
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

    public function group()
    {
        return $this->belongsTo(HQConfigurationGroup::class, 'group_id');
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }

    public function versions()
    {
        return $this->hasMany(HQConfigurationVersion::class, 'configuration_id');
    }

    public function changes()
    {
        return $this->hasMany(HQConfigurationChange::class, 'configuration_id');
    }
}
