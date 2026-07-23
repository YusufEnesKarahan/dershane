<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutiveReport extends Model
{
    protected $fillable = ['title', 'description', 'content_data'];

    protected $casts = [
        'content_data' => 'array',
    ];
}
