<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQConfigurationChange extends Model
{
    use HasFactory;

    protected $table = 'hq_configuration_changes';

    protected $fillable = [
        'uuid',
        'configuration_id',
        'old_value',
        'new_value',
        'changed_by',
    ];

    protected $casts = [
        'old_value' => 'json',
        'new_value' => 'json',
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
