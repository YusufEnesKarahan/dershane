<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQConfigurationLog extends Model
{
    use HasFactory;

    protected $table = 'hq_configuration_logs';

    protected $fillable = [
        'profile_id',
        'system_instance_id',
        'action',
        'status',
        'old_value',
        'new_value',
        'response',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'response' => 'array',
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

    public function profile()
    {
        return $this->belongsTo(HQConfigurationProfile::class, 'profile_id');
    }

    public function systemInstance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'system_instance_id');
    }
}
