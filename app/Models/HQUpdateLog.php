<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQUpdateLog extends Model
{
    use HasFactory;

    protected $table = 'hq_update_logs';

    protected $fillable = [
        'uuid',
        'update_id',
        'action',
        'status',
        'message',
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

    public function updateRecord()
    {
        return $this->belongsTo(HQUpdate::class, 'update_id');
    }
}
