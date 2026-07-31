<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQConfigurationVersion extends Model
{
    use HasFactory;

    protected $table = 'hq_configuration_versions';

    protected $fillable = [
        'uuid',
        'configuration_id',
        'version_tag',
        'value',
        'created_by',
    ];

    protected $casts = [
        'value' => 'json',
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

    public function configuration()
    {
        return $this->belongsTo(HQConfiguration::class, 'configuration_id');
    }
}
