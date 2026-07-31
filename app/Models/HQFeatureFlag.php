<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQFeatureFlag extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_feature_flags';

    protected $fillable = [
        'uuid',
        'name',
        'key',
        'description',
        'is_enabled',
        'rules',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'rules' => 'json',
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

    public function targets()
    {
        return $this->hasMany(HQFeatureFlagTarget::class, 'feature_flag_id');
    }
}
