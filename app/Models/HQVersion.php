<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HQVersion extends Model
{
    protected $table = 'hq_versions';

    protected $fillable = [
        'uuid',
        'version',
        'channel',
        'release_notes',
        'minimum_supported_version',
        'is_mandatory',
        'status',
        'published_at',
        'created_by'
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function jobs()
    {
        return $this->hasMany(HQUpdateJob::class, 'version_id');
    }
}
