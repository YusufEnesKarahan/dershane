<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQExtension extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_extensions';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'vendor',
        'version',
        'status',
        'type',
        'metadata',
        'installed_at',
    ];

    protected $casts = [
        'metadata' => 'json',
        'installed_at' => 'datetime',
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

    public function versions()
    {
        return $this->hasMany(HQExtensionVersion::class, 'extension_id');
    }

    public function installations()
    {
        return $this->hasMany(HQExtensionInstallation::class, 'extension_id');
    }

    public function permissions()
    {
        return $this->hasMany(HQExtensionPermission::class, 'extension_id');
    }

    public function configs()
    {
        return $this->hasMany(HQExtensionConfig::class, 'extension_id');
    }
}
