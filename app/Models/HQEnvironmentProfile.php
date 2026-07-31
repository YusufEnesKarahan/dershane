<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQEnvironmentProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_environment_profiles';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'overrides',
        'is_active',
    ];

    protected $casts = [
        'overrides' => 'json',
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
}
