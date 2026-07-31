<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQExtensionEvent extends Model
{
    use HasFactory;

    protected $table = 'hq_extension_events';

    protected $fillable = [
        'uuid',
        'extension_id',
        'event_name',
        'payload',
    ];

    protected $casts = [
        'payload' => 'json',
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
