<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQUpdate extends Model
{
    use HasFactory;

    protected $table = 'hq_updates';

    protected $fillable = [
        'uuid',
        'version',
        'channel',
        'package_url',
        'checksum',
        'status',
        'released_at',
        'installed_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'released_at' => 'datetime',
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

    public function logs()
    {
        return $this->hasMany(HQUpdateLog::class, 'update_id');
    }
}
