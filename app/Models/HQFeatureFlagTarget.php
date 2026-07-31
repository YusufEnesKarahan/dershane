<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQFeatureFlagTarget extends Model
{
    use HasFactory;

    protected $table = 'hq_feature_flag_targets';

    protected $fillable = [
        'uuid',
        'feature_flag_id',
        'target_type',
        'target_id',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
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

    public function featureFlag()
    {
        return $this->belongsTo(HQFeatureFlag::class, 'feature_flag_id');
    }
}
