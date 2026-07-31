<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQExtensionVersion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_extension_versions';

    protected $fillable = [
        'uuid',
        'extension_id',
        'version',
        'release_notes',
        'requirements',
        'dependencies',
        'status',
    ];

    protected $casts = [
        'requirements' => 'json',
        'dependencies' => 'json',
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
}
