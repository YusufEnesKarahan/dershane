<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQLog extends Model
{
    use HasFactory;

    protected $table = 'hq_logs';
    public $timestamps = false; // We use created_at manually in migration

    protected $fillable = [
        'uuid',
        'tenant_id',
        'service',
        'level',
        'message',
        'context',
        'trace_id',
        'created_at',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
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

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
