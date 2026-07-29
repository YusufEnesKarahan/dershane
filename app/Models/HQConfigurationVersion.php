<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQConfigurationVersion extends Model
{
    use HasFactory;

    protected $table = 'hq_configuration_versions';

    protected $fillable = [
        'profile_id',
        'version',
        'created_by',
        'notes',
        'configuration_snapshot',
    ];

    protected $casts = [
        'configuration_snapshot' => 'array',
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

    public function profile()
    {
        return $this->belongsTo(HQConfigurationProfile::class, 'profile_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
