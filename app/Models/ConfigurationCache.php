<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurationCache extends Model
{
    use HasFactory;

    protected $table = 'configuration_cache';

    protected $fillable = [
        'key',
        'value',
        'type',
        'last_synced_at',
        'version',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];
}
