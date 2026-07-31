<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQExtensionInstallation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_extension_installations';

    protected $fillable = [
        'uuid',
        'extension_id',
        'tenant_id',
        'version_id',
        'status',
        'enabled_at',
        'disabled_at',
    ];

    protected $casts = [
        'enabled_at' => 'datetime',
        'disabled_at' => 'datetime',
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

    public function extension()
    {
        return $this->belongsTo(HQExtension::class, 'extension_id');
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }

    public function version()
    {
        return $this->belongsTo(HQExtensionVersion::class, 'version_id');
    }
}
