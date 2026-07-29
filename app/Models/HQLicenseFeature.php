<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQLicenseFeature extends Model
{
    use HasFactory;

    protected $table = 'hq_license_features';

    protected $fillable = [
        'license_id',
        'feature_name',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
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

    public function license()
    {
        return $this->belongsTo(HQLicense::class, 'license_id');
    }
}
