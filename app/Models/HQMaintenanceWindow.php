<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQMaintenanceWindow extends Model
{
    use HasFactory;

    protected $table = 'hq_maintenance_windows';

    protected $fillable = [
        'targetable_type',
        'targetable_id',
        'starts_at',
        'ends_at',
        'reason',
        'auto_manage',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'auto_manage' => 'boolean',
    ];

    public function targetable()
    {
        return $this->morphTo();
    }
}
