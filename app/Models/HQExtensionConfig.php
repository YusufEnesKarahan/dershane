<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQExtensionConfig extends Model
{
    use HasFactory;

    protected $table = 'hq_extension_configs';

    protected $fillable = [
        'uuid',
        'extension_id',
        'tenant_id',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'json',
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
}
