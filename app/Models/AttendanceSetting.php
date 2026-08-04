<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;

class AttendanceSetting extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'branch_id',
        'late_threshold_minutes',
        'require_note_for_absence',
        'auto_create_from_schedule',
    ];

    protected $casts = [
        'late_threshold_minutes' => 'integer',
        'require_note_for_absence' => 'boolean',
        'auto_create_from_schedule' => 'boolean',
    ];
}
