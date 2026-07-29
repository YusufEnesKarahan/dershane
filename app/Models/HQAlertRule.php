<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQAlertRule extends Model
{
    use HasFactory;

    protected $table = 'hq_alert_rules';

    protected $fillable = [
        'uuid',
        'name',
        'category',
        'severity',
        'event_type',
        'condition',
        'is_active',
        'cooldown_minutes',
        'created_by',
    ];

    protected $casts = [
        'condition' => 'array',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
