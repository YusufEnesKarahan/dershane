<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQBackupStorageLocation extends Model
{
    use HasFactory;

    protected $table = 'hq_backup_storage_locations';

    protected $fillable = [
        'uuid',
        'name',
        'driver',
        'credentials',
        'is_active',
        'capacity_bytes',
        'used_bytes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credentials' => 'encrypted:json', // Automatically encrypt/decrypt and cast to array
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

    public function policies()
    {
        return $this->hasMany(HQBackupPolicy::class, 'hq_backup_storage_location_id');
    }
}
