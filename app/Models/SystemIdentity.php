<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SystemIdentity extends Model
{
    protected $table = 'system_identity';
    
    // We are using a UUID as the primary key.
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'installation_uuid',
        'product_name',
        'product_version',
        'license_key',
        'branch_count',
        'company_name',
        'brand_name',
    ];

    protected $casts = [
        'branch_count' => 'integer',
    ];

    /**
     * Boot the model to automatically generate UUIDs if missing.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->installation_uuid)) {
                $model->installation_uuid = (string) Str::uuid();
            }
        });
    }
}
