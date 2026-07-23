<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsCache extends Model
{
    protected $table = 'analytics_cache';

    protected $fillable = ['cache_key', 'cache_value', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
