<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupCache extends Model
{
    use HasFactory;

    protected $table = 'backup_cache';

    protected $fillable = [
        'system_uuid',
        'last_backup_at',
        'status',
        'metadata',
    ];

    protected $casts = [
        'last_backup_at' => 'datetime',
        'metadata' => 'array',
    ];
}
