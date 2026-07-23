<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardSnapshot extends Model
{
    protected $fillable = ['metrics'];

    protected $casts = [
        'metrics' => 'array',
    ];
}
