<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQExtensionPermission extends Model
{
    use HasFactory;

    protected $table = 'hq_extension_permissions';

    protected $fillable = [
        'uuid',
        'extension_id',
        'permission_key',
        'description',
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
