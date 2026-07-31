<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQSecretVault extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_secret_vaults';

    protected $fillable = [
        'uuid',
        'name',
        'key',
        'encrypted_value',
        'description',
        'expires_at',
        'rotation_interval',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
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

    public function versions()
    {
        return $this->hasMany(HQSecretVersion::class, 'secret_vault_id');
    }
}
