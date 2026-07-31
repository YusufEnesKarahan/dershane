<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQConfigurationRollback extends Model
{
    use HasFactory;

    protected $table = 'hq_configuration_rollbacks';

    public $timestamps = false; // We only use rolled_back_at manually, or we can just leave timestamps false

    protected $fillable = [
        'uuid',
        'configuration_id',
        'version_id',
        'from_value',
        'to_value',
        'executed_by',
        'rolled_back_at',
    ];

    protected $casts = [
        'from_value' => 'json',
        'to_value' => 'json',
        'rolled_back_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->rolled_back_at)) {
                $model->rolled_back_at = now();
            }
        });
    }

    public function configuration()
    {
        return $this->belongsTo(HQConfiguration::class, 'configuration_id');
    }

    public function version()
    {
        return $this->belongsTo(HQConfigurationVersion::class, 'version_id');
    }
}
